<?php

namespace App\Services;

use App\Dto\CoinDto;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterTx;
use Psr\Log\LoggerInterface;

/**
 * Class PoolsArbitrator
 * @package App\Services
 */
class PoolsArbitrator
{
    private LoggerInterface $logger;

    /**
     * PoolsArbitrator.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * signTx.
     *
     * @param array $route
     * @param int $txAmount
     * @param MinterAPI $api
     * @param string $walletAddress
     * @param string $walletPk
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function signTx(
        array $route,
        int $txAmount,
        MinterAPI $api,
        string $walletAddress,
        string $walletPk
    ) : string {
        $fee = count($route) > 4 ? 2 + ((count($route)-4) * 0.25) : 2;
        $data = new MinterSellSwapPoolTx(
            array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
            $txAmount,
            $txAmount + $fee
        );
        $nonce = $api->getNonce($walletAddress);
        $tx = new MinterTx($nonce, $data);

        return $tx->sign($walletPk);
    }

    /**
     * arbitrate.
     *
     * @param CoinDto[][] $routes
     * @param int $txAmount
     * @param MinterAPI $readApi
     * @param MinterAPI $writeApi
     * @param int $reqDelay
     * @param int $walletIdx
     * @param array $wallets
     *
     * @return void
     */
    public function arbitrate(
        array $routes,
        int $txAmount,
        MinterAPI $readApi,
        MinterAPI $writeApi,
        int $reqDelay,
        int $walletIdx,
        array $wallets,
    ) : void {
        $walletAddress = $wallets[$walletIdx]['wallet'];
        $walletPk = $wallets[$walletIdx]['pk'];

        foreach ($routes as $route) {
            try {
                try {
                    $this->logger->info('R: {route}', [
                        'route' => implode('=>', array_map(
                            static fn(CoinDto $coin) => $coin->getSymbol(),
                            $route,
                        )),
                    ]);
                    $signedTx = $this->signTx($route, $txAmount, $readApi, $walletAddress, $walletPk);
                    $response = $writeApi->send($signedTx);
                    $this->logger->info('R: {route}', [
                        'response' => $response,
                        'route' => implode('=>', array_map(
                            static fn(CoinDto $coin) => $coin->getSymbol(),
                            $route,
                        )),
                    ]);
                    // after successful tx change wallet to make new tx from new wallet
                    $walletIdx = isset($wallets[$walletIdx + 1]) ? $walletIdx + 1 : 0;
                    $walletAddress = $wallets[$walletIdx]['wallet'];
                    $walletPk = $wallets[$walletIdx]['pk'];
                    $this->logger->info("\tChange wallet to: {walletIdx}: {walletAddress}", [
                        'walletIdx' => $walletIdx,
                        'walletAddress' => $walletAddress,
                    ]);
                    usleep($reqDelay);
                } catch (ClientException $e) {
                    $response = $e->getResponse();
                    $content = $response->getBody()->getContents();
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                    if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                        $errorData = $data['error']['data'];
                        $this->logger->debug('Want: {maxValToSell}. Got: {neededSpendVal}. Coin: {coinSymbol}', [
                            'maxValToSell' => $errorData['maximum_value_to_sell'],
                            'neededSpendVal' => $errorData['needed_spend_value'],
                            'coinSymbol' => $errorData['coin_symbol'],
                        ]);
                    } else {
                        $this->logger->error($e->getMessage(), [
                            'content' => $e->getResponse()->getBody()->getContents(),
                            'message' => $e->getMessage(),
                            'class' => $e::class,
                            'file' => $e->getFile(),
                            'code' => $e->getLine(),
                        ]);
                    }
                }
            } catch (ServerException $e) {
                $this->logger->critical('{code}: {phrase}', [
                    'code' => $e->getResponse()->getStatusCode(),
                    'phrase' => $e->getResponse()->getReasonPhrase(),
                ]);
                sleep(2);
            } catch (GuzzleException $e) {
                $this->logger->critical($e->getMessage(), [
                    'message' => $e->getMessage(),
                    'class' => $e::class,
                    'file' => $e->getFile(),
                    'code' => $e->getLine(),
                ]);

                sleep(10);
            }
        }
    }
}
