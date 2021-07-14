<?php


namespace App\Command\Node;

use App\Dto\CoinDto;
use App\Services\PoolsStore;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Pheanstalk\Pheanstalk;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

/**
 * Class ConsumeNodeMessagesCommand
 * @package App\Command\Node
 */
class ConsumeNodeMessagesCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'node:msg:consume';

    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * cache.
     *
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * PollsArbitrationCommand.
     *
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $logger,
        CacheInterface $cache
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Consume messages published by local miner node.');
    }

    /**
     * store.
     *
     * @var PoolsStore|null
     */
    private ?PoolsStore $store = null;

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $pheanstalk = Pheanstalk::create('0.0.0.0');
        dump($pheanstalk->stats());
        $this->store = new PoolsStore();
        $this->store->allCoins();


        while (true) {
//            $this->logger->info('Iterate');
            $job = $pheanstalk->watch('mnode-tube')->reserve();

            if (isset($job)) {
                try {
                    $jobPayload = $job->getData();
//                    dump($jobPayload);
                    $this->handleJob(json_decode($jobPayload, true, 512, JSON_THROW_ON_ERROR));


//                    sleep(2);
//                    // If it's going to take a long time, periodically
//                    // tell beanstalk we're alive to stop it rescheduling the job.
//                    $pheanstalk->touch($job);
//                    sleep(2);

                    // eventually we're done, delete job.
                    $pheanstalk->delete($job);
                } catch (Throwable $e) {
                    $this->logger->critical($e->getMessage(), [
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                    // handle exception.
                    // and let some other worker retry.
                    $pheanstalk->delete($job);
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallet = [
        'wallet' => 'Mx08ae486eee85c7dd83f2f6972f614965110ebb60',
        'pk' => 'e0422daaba555f43dbc9a7cc410c1a9adae5bbbbba90bc79e01340f0046d6c4d',
    ];

    /**
     * handleJob.
     *
     * @param array $data
     *
     * @return void
     */
    private function handleJob(array $data) : void
    {
//        dump($data);
        $tags = $data['tags'];
        $reqDelay = 0;
//        $api = new MinterAPI('http://0.0.0.0:8843/v2/');
        $api = new MinterAPI('https://api.minter.one/v2/');

        if ($tags['tx.coin_to_buy'] !== $tags['tx.coin_to_sell']) {
            $coins = [];
            $routes = array_values($data['routes']);

            if (count($routes) === 1) {
                return;
            }

            $firstRoute = $routes[0];
            $preLastRoute = $routes[count($routes) - 2];
            $lastRoute = $routes[count($routes) - 1];

            foreach ($data['routes'] as $route) {
                $coins[] = $this->store->coinById($route['coin_in']);
                $coins[] = $this->store->coinById($route['coin_out']);
            }

            $r = implode('=>', array_map(
                static fn(CoinDto $coin) => $coin->getSymbol(),
                $coins,
            ));
            $this->logger->info("R: $r");
            $sellAmount = MinterConverter::convertToBase($firstRoute['value_in']);
            $getAmount = MinterConverter::convertToBase($lastRoute['value_out']);
            $this->logger->info("Sell amnt: $sellAmount => Get amnt: $getAmount");
            $this->logger->info("Block: {$data['block']}");

            // looking for preferred route
            $pool = $this->preferredPools[$lastRoute['coin_out']][$lastRoute['coin_in']] ?? [];

            if (!$pool && (int) $lastRoute['coin_out'] !== 0 && (int) $lastRoute['coin_in'] !== 0) {
                $coinOut = (int) $lastRoute['coin_out'];
                $coinIn = (int) $lastRoute['coin_in'];

                if ($coinOut === 0) {
                    $coinOut = (int) $preLastRoute['coin_out'];
                    $coinIn = (int) $preLastRoute['coin_in'];
                }
                $pool = $this->lookingPool(
                    $this->store->coinById($coinOut)->getSymbol(),
                    $this->store->coinById($coinIn)->getSymbol(),
                );
//                $pool = [0, (int) $lastRoute['coin_out'], (int) $lastRoute['coin_in'], 0];
            }

            if ($pool) {
                try {
                    try {
                        $route = [0, $pool[0], $pool[1], 0];
                        $this->logger->info(sprintf('Create TX for route: %s', implode('=>', $route)));
                        $amount = 5;
                        $fee = 2;
                        $minGasPrice = 1;
//                        $minAmountToBuy = 5 + ($fee * $minGasPrice);
                        $minAmountToBuy = 4.95;
                        $txdata = new MinterSellSwapPoolTx($route, $amount, $minAmountToBuy);
                        $nonce = $api->getNonce($this->wallet['wallet']);
                        $tx = (new MinterTx($nonce, $txdata))->setGasPrice($minGasPrice);
                        $signedTx = $tx->sign($this->wallet['pk']);
                        $response = $api->send($signedTx);
//                        $this->logger->info("R: {$r}");
                        $this->logger->info("\t Amount: {$amount}. Min Buy Amount {$minAmountToBuy}. Gas Price: {$minGasPrice}");
                        $this->logger->info("\t", ['response' => (array) $response]);
                        $this->logger->info("\t", ['block' => $api->getStatus()->latest_block_height]);
                    } catch (ClientException $e) {
                        $response = $e->getResponse();
                        $content = $response->getBody()->getContents();
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                        if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                            $errorData = $data['error']['data'];
                            $this->logger->info(sprintf(
                                'Want: %s - Got: %s => Coin: %s',
                                MinterConverter::convertToBase($errorData['maximum_value_to_sell']),
                                MinterConverter::convertToBase($errorData['needed_spend_value']),
                                $errorData['coin_symbol'],
                            ));
                        } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 701) {
                            $errorData = $data['error']['data'];
                            $this->logger->info(sprintf(
                                'Swap pool not exists: %s - %s',
                                $errorData['coin0'],
                                $errorData['coin1'],
                            ));
                        } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 107) {
                            $this->logger->info("Insufficient funds for sender account R: {$r}");
                        } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 114) {
                            $this->logger->info($data['error']['message'], $data['error']['data']);
                        } else {
                            $this->logger->error($e->getMessage(), [
                                'content' => $e->getResponse()->getBody()->getContents(),
                                'class' => $e::class,
                                'file' => $e->getFile(),
                                'code' => $e->getLine(),
                            ]);
                        }
                    }
                } catch (ServerException $e) {
                    $this->logger->error($e->getMessage());
                } catch (GuzzleException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        // И так. Пример. По транзакции - BIP=>HUB=>HUB=>LIQUIDHUB
        // Нужно провести BIP=>MONEHUB=>LIQUIDHUB=>BIP
        // Потому что:
        //  - LIQUIDHUB / MONEHUB - пул с наибольшими торгами
        // - Цена HUB=>LIQUIDHUB изменилась, а MONEHUB=>LIQUIDHUB еще не отыграла это изменение (по идее)
        //    т.е. мы купим MONEHUB, за него LIQUIDHUB и выровняем пул.
        // LIQUIDHUB => [
        //  HUB => [MONEHUB, LIQUIDHUB]
        //  MONEHUB => [HUB, LIQUIDHUB]
        //  MONSTERHUB => [MONEHUB, LIQUIDHUB]
        //  BIP => [MONEHUB, LIQUIDHUB]
        //  HUBCHAIN => [MONEHUB, LIQUIDHUB]
        //  ONLY1HUB => [MONEHUB, LIQUIDHUB]
        //  TRUSTHUB => [MONEHUB, LIQUIDHUB]
        //]
    }

    public function lookingPool(string $coinOutSymbol, string $coinInSymbol) : array
    {
        // looking pools for coin out
        $cacheKey = "coin_pools_{$coinOutSymbol}";

        $coinPools = $this->cache->get($cacheKey, function (ItemInterface $item) use ($cacheKey, $coinOutSymbol) {
            $this->logger->info("\tCaching: $cacheKey");
            $item->expiresAfter(3600);
            $resp = file_get_contents("https://explorer-api.minter.network/api/v2/pools?coin=$coinOutSymbol");

            return json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
        });

        // Then looking for pool excluding coinIn
        foreach ($coinPools['data'] as $pool) {
            if ($pool['coin0']['symbol'] === $coinOutSymbol || $pool['coin1'] === $coinOutSymbol) {
                // Check if pool is not for coinIn
                if (
                    $pool['coin0'] !== $coinInSymbol
                    && $pool['coin1'] !== $coinInSymbol
                    && (int) $pool['trade_volume_bip_1d'] > 1000
                ) {
                    $this->logger->info("\tFound pool: {$pool['coin0']['symbol']}=>{$pool['coin1']['symbol']}");

                    return [$pool['coin0']['id'], $pool['coin1']['id']];
                }
            }
        }

        return [];
    }


    private array $preferredPools = [
        // LIQUIDHUB this is coin_out
        1893 => [
            // this is coin_in
            1902 => [1901, 1893], // HUB => [MONEHUB, LIQUIDHUB]
            1901 => [1902, 1893], // MONEHUB => [HUB, LIQUIDHUB]
            1895 => [1901, 1893], //MONSTERHUB => [MONEHUB, LIQUIDHUB]
            0 => [1901, 1893], //BIP => [MONEHUB, LIQUIDHUB]
            1900 => [1901, 1893], //BIP => [MONEHUB, LIQUIDHUB]
            2025 => [1901, 1893], //BIP => [MONEHUB, LIQUIDHUB]
            1903 => [1901, 1893], //BIP => [MONEHUB, LIQUIDHUB]
        ],
        // HUBABUBA
        1942 => [
            1902 => [1901, 1893],
        ],
    ];
}
