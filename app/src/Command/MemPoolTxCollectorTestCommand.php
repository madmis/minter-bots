<?php

namespace App\Command;

use App\Dto\CoinDto;
use App\Services\PoolsStore;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use JsonException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

/**
 * Class MemPoolTxCollectorCommand
 * @package App\Command
 */
class MemPoolTxCollectorTestCommand extends Command
{
    protected static $defaultName = 'app:test:mempool:tx:collector';

    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * bus.
     *
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;

    /**
     * PollsArbitrationCommand.
     *
     * @param LoggerInterface $logger
     * @param MessageBusInterface $bus
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->bus = $bus;
    }

    protected function configure() : void
    {
        $this->setDescription('Mempool TX Collector.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $indexedCoins = (new PoolsStore())->coinsIndexedById();
        $readApi = new MinterAPI('https://api.minter.one/v2/');
        $writeApi = new MinterAPI('https://api.minter.one/v2/');
        $walletsFile = '/var/www/ccbip/resources/wallets/2-be1564-858cfa.json';
        /** @noinspection PhpUnhandledExceptionInspection */
        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));
        $walletIdx = array_rand($wallets);
        $walletAddress = $wallets[$walletIdx]['wallet'];
        $walletPk = $wallets[$walletIdx]['pk'];
        $reqDelay = 0;

        while (true) {
            try {
                $txs = $readApi->getUnconfirmedTxs();
                $nonce = $readApi->getNonce($walletAddress);
            } catch (RequestException | ConnectException) {
                sleep(1);
                continue;
            }
//            printf("Found Txs: %s\n", count($txs->transactions));
//            printf("\tBase64: %s\n", base64_encode(json_encode($txs->transactions)));
            foreach ($txs->transactions as $tx) {
                preg_match("/{(.*?)}/", $tx, $matches);

                if (!empty($matches[1])) {
                    try {
                        $mtx = MinterTx::decode($matches[1]);
                    } catch (Throwable $e) {
                        continue;
                    }

                    /** @var MinterSellSwapPoolTx $txData */
                    $txData = $mtx->getData();

//                    if (in_array($txData->getType(), [24, 25, 23], true)) {
                    if ($txData->getType() === 23) {
//                        $indexedCoins

                        dump($txData->coins);
                        dump($txData);

                        $first = $txData->coins[array_key_first($txData->coins)];
                        $last = $txData->coins[array_key_last($txData->coins)];

                        if ($first !== $last && $txData->coins < 4 && !($txData->minimumValueToBuy > 0)) {
                            dump($txData);
                            $coins = $txData->coins;
                            array_unshift($coins, 0);
                            $coins[] = 0;
//
//
//
//
//                            $rCoins = $coins = [];
//                            foreach ($txData->coins as $coinId) {
//                                if (isset($indexedCoins[$coinId])) {
//                                    $rCoins[] = $indexedCoins[$coinId]->getSymbol();
//
//                                    if ($coinId > 0) {
//                                        $coins[$coinId] = $indexedCoins[$coinId]->getSymbol();
//                                    }
//                                } else {
//                                    $rCoins[] = $coinId;
//                                }
//                            }
//                            printf("Type: %s (%s)\n", $txData->getType(), (new DateTimeImmutable())->format('H:i:s'));
//                            printf("\tRoute: %s\n", implode('=>', $rCoins));
//
//                            $minBuy = 10000000;
//                            if (isset($txData->minimumValueToBuy)) {
//                                $minBuy = $txData->minimumValueToBuy;
//                                printf("\tMinBuy: %s. Sell: %.10f\n", $txData->minimumValueToBuy, $txData->valueToSell ?? 0);
//                            }
//
//                            if ($minBuy > 0) {
//                                continue;
//                            }
//
//                            printf("\tSender: %s\n", $mtx->getSenderAddress());

//                            if (count($coins) > 1 && count($coins) < 4) {
                            $amount = 2000;
//                                $route = [$indexedCoins[0]];
//                                foreach (array_reverse(array_keys($coins)) as $coinId) {
//                                    $route[] = $indexedCoins[$coinId];
//                                }
//                                $route[] = $indexedCoins[0];

                            try {
                                try {
//                                        $r = implode('=>', array_map(
//                                            static fn(CoinDto $coin) => $coin->getSymbol(),
//                                            $route,
//                                        ));
//                                        $this->logger->debug("R: {$r}");
//                                    $signedTx = $this->signTx(
//                                        $coins,
//                                        $amount,
//                                        $readApi,
//                                        $walletAddress,
//                                        $walletPk,
//                                        $nonce,
//                                    );
//                                    $opts = [
//                                        'http' => [
//                                            'method' => 'GET',
//                                            'header' => 'Content-Type: application/json',
//                                        ],
//                                    ];

//                                    $context = stream_context_create($opts);
//                                    $result = file_get_contents("https://api.minter.one/v2/send_transaction/{$signedTx}");
//                                    dump($result);
//                                        $response = $writeApi->send($signedTx);
//                                        $this->logger->info("R: {$r}");
//                                        $this->logger->info("\t", ['response' => (array) $response]);
//                                        $this->logger->info("\t", ['block' => $readApi->getStatus()->latest_block_height]);
                                } catch (ClientException $e) {
                                    $response = $e->getResponse();
                                    $content = $response->getBody()->getContents();
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                                    if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                                        $errorData = $data['error']['data'];
                                        $this->logger->debug(sprintf(
                                            'Want: %s - Got: %s => Coin: %s',
                                            MinterConverter::convertToBase($errorData['maximum_value_to_sell']),
                                            MinterConverter::convertToBase($errorData['needed_spend_value']),
                                            $errorData['coin_symbol'],
                                        ));
                                    } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 701) {
                                        $errorData = $data['error']['data'];
                                        $this->logger->debug(sprintf(
                                            'Swap pool not exists: %s - %s',
                                            $errorData['coin0'],
                                            $errorData['coin1'],
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
                                sleep(1);
                            } catch (GuzzleException $e) {
                                sleep(3);
                            }
                            printf("\tRoute: %s\n", implode('=>', $coins));
//                            }
                        }
                    }
                }
            }
        }

        return self::SUCCESS;
    }

    private function estimateCoinSell(float $amount, array $route) : string
    {
        $pipAmnt = MinterConverter::convertToPip((string) $amount);
        $query = "value_to_sell={$pipAmnt}&coin_id_to_buy=0&coin_id_to_sell=0&swap_from=pool";
        foreach ($route as $coinId) {
            $query .= "&route={$coinId}";
        }
        $uri = "https://api.minter.one/v2/estimate_coin_sell?{$query}";
        dump($uri);
        try {
            $response = (new Client())->get($uri);
            $contents = $response->getBody()->getContents();
            dump($contents);
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            return MinterConverter::convertToBase($data['will_get']);
        } catch (RequestException | ConnectException | JsonException) {

        }

        return 0;
    }

    /**
     * signTx.
     *
     * @param array $route
     * @param float $txAmount
     * @param MinterAPI $api
     * @param string $walletAddress
     * @param string $walletPk
     * @param int $nonce
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
        int $nonce,
    ) : string {
//        $fee = count($route) > 4 ? 2 + ((count($route) - 4) * 0.25) : 2;

//        $minGetAmount = $txAmount - $fee;
//        $minGetAmount = $txAmount + $fee;
        $minGetAmount = $txAmount - ($txAmount * 0.01);
        $this->logger->debug("\tAmount: {$txAmount}. Min get amount: {$minGetAmount}");

        $data = new MinterSellSwapPoolTx(
//            array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
            $route,
            $txAmount,
            $minGetAmount
        );
//        $nonce = $api->getNonce($walletAddress);
        $tx = new MinterTx($nonce, $data);

        return $tx->sign($walletPk);
    }
}
