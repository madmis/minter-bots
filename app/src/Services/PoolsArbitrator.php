<?php

namespace App\Services;

use App\Dto\CoinDto;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterBuySwapPoolTx;
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
     * @param float $txAmount
     * @param MinterAPI $api
     * @param string $walletAddress
     * @param string $walletPk
     * @param bool $isCustomRoute
     * @param float $oneBipInCustomCoinPrice
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function signTx(
        array $route,
        float $txAmount,
        MinterAPI $api,
        string $walletAddress,
        string $walletPk,
        bool $isCustomRoute = false,
        float $oneBipInCustomCoinPrice = 0.0,
    ) : string {
        $fee = count($route) > 4 ? 2 + ((count($route) - 4) * 0.25) : 2;

        if ($isCustomRoute) {
            $fee *= $oneBipInCustomCoinPrice;
        }

        $minGetAmount = $txAmount + $fee;
        $this->logger->debug("\tAmount: {$txAmount}. Min get amount: {$minGetAmount}");

        $data = new MinterSellSwapPoolTx(
            array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
            $txAmount,
            $minGetAmount
        );
        $nonce = $api->getNonce($walletAddress);
        $tx = new MinterTx($nonce, $data);

        return $tx->sign($walletPk);
    }

    /**
     * arbitrate.
     *
     * @param CoinDto[][] $routes
     * @param float $txAmount
     * @param MinterAPI $readApi
     * @param MinterAPI $writeApi
     * @param int $reqDelay
     * @param int $walletIdx
     * @param array $wallets
     * @param bool $isCustomRoute
     * @param float $oneBipInCustomCoinPrice
     *
     * @return void
     */
    public function arbitrate(
        array $routes,
        float $txAmount,
        MinterAPI $readApi,
        MinterAPI $writeApi,
        int $reqDelay,
        int $walletIdx,
        array $wallets,
        bool $isCustomRoute = false,
        float $oneBipInCustomCoinPrice = 0.0,
    ) : void {
        $walletAddress = $wallets[$walletIdx]['wallet'];
        $walletPk = $wallets[$walletIdx]['pk'];

        foreach ($routes as $route) {
            try {
                try {
                    $r = implode('=>', array_map(
                        static fn(CoinDto $coin) => $coin->getSymbol(),
                        $route,
                    ));
                    $this->logger->debug("R: {$r}");
//                    $this->logger->info("W: {$walletAddress}");

                    $signedTx = $this->signTx(
                        $route,
                        $txAmount,
                        $readApi,
                        $walletAddress,
                        $walletPk,
                        $isCustomRoute,
                        $oneBipInCustomCoinPrice
                    );
                    $response = $writeApi->send($signedTx);
                    $this->logger->info("R: {$r}");
                    $this->logger->info("\t", ['response' => (array) $response]);
                    $this->logger->info("\t", ['block' => $readApi->getStatus()->latest_block_height]);

                    // after successful tx change wallet to make new tx from new wallet
                    $oldWallet = $walletAddress;
                    $walletIdx = isset($wallets[$walletIdx + 1]) ? $walletIdx + 1 : 0;
                    $walletAddress = $wallets[$walletIdx]['wallet'];
                    $walletPk = $wallets[$walletIdx]['pk'];
                    $this->logger->info(sprintf("\tChange wallet from %s => to: %s: %s", $oldWallet, $walletIdx, $walletAddress));
                    usleep($reqDelay);
                } catch (ClientException $e) {
                    $response = $e->getResponse();
                    $content = $response->getBody()->getContents();
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                    if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                        $errorData = $data['error']['data'];
                        $this->logger->debug(sprintf(
                            'Want: %.02f - Got: %.02f => Coin: %s',
                            $errorData['maximum_value_to_sell'],
                            $errorData['needed_spend_value'],
                            $errorData['coin_symbol'],
                        ));
                    } else {
                        $this->logger->error($e->getMessage(), [
                            'content' => $e->getResponse()->getBody()->getContents(),
                            'class' => $e::class,
                            'file' => $e->getFile(),
                            'code' => $e->getLine(),
                        ]);
                    }
                    usleep($reqDelay);
                }
            } catch (ServerException $e) {
//                $this->logger->critical(sprintf(
//                    '%s: %s',
//                    $e->getResponse()->getStatusCode(),
//                    $e->getResponse()->getReasonPhrase(),
//                ));
                sleep(1);
            } catch (GuzzleException $e) {
//                $this->logger->critical($e->getMessage(), [
//                    'class' => $e::class,
//                    'file' => $e->getFile(),
//                    'code' => $e->getLine(),
//                ]);

                sleep(3);
            }
        }
    }
}
