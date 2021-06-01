<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\Command;

use Amp\ByteStream\Payload;
use Amp\Dns\DnsException;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Websocket\Client\Connection;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use App\Dto\CoinDto;
use App\Services\PoolsStore;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterBuyCoinTx;
use Minter\SDK\MinterCoins\MinterCreateCoinTx;
use Minter\SDK\MinterCoins\MinterSellAllCoinTx;
use Minter\SDK\MinterCoins\MinterSellCoinTx;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Minter\SDK\MinterWallet;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function Amp\coroutine;
use function Amp\Promise\all;
use function Amp\Websocket\Client\connect;

/**
 * Class TestCommand
 * @package App\Command
 */
class TestCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:test';

    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * PollsArbitrationCommand.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();

        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->addOption('req-delay', 'd', InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 0)
            ->addOption('tx-amounts', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Transaction amounts', [3000, 2000, 1000])
            ->addOption(
                'wallets-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to JSON wallets file',
                '/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json'
            )
            ->setDescription('Test command.');
    }

    /**
     * loopRun.
     *
     * @param array $pools
     * @param array $wallets
     *
     * @return void
     */
//    private function loopRun(array $pools, array $wallets) : void
//    {
//        $client = new Client(['base_uri' => 'https://api.minter.one']);
//
//        try {
//            Loop::run(function () use ($pools, $wallets, $client) {
//                /** @var Connection $connection */
//                $connection = yield connect('wss://explorer-rtm.minter.network/connection/websocket');
//                yield $connection->send('{"id":1}');
//
//                $i = 0;
//
//
//                /** @var Message $message */
//                while ($message = yield $connection->receive()) {
//                    $payload = yield $message->buffer();
//
////                    printf("Received: %s\n", $payload);
//
//                    try {
//                        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
//
//                        if (isset($data['id']) && $data['id'] === 1) {
//                            yield $connection->send('{"method":1,"params":{"channel":"transactions_100"},"id":3}');
//                        }
//
//                        if (isset($data['result']['channel']) && $data['result']['channel'] === 'transactions_100') {
////                        dump($data);
//                            $channelData = $data['result']['data']['data'];
//                            // Types
//                            // 4 MinterBuyCoinTx, 2 MinterSellCoinTx, 3 MinterSellAllCoinTx,
//                            // 24 MinterBuySwapPoolTx, 23 MinterSellSwapPoolTx, 25 MinterSellAllSwapPoolTx
//                            // Нам нужны только транзакции в пулах
//                            if (in_array((int) $channelData['type'], [24, 25, 23], true)) {
//                                $coinsData = $channelData['data'];
//                                $sellS = $coinsData['coin_to_sell']['symbol'];
//                                $buyS = $coinsData['coin_to_buy']['symbol'];
//
////                                $resp = $client->get('/v2/status');
////                                $status = json_decode($resp->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
//
//                                printf("Type: %s\n", $channelData['type']);
//                                printf("\tBuy: %s\n", $coinsData['coin_to_buy']['symbol']);
//                                printf("\tSell: %s\n", $coinsData['coin_to_sell']['symbol']);
////                                printf("\t\tSTATUS: block: %s, block time: %s\n", $status['latest_block_height'], $status['latest_block_time']);
//                                printf("\t\tTX time - Local time: %s - %s\n", $channelData['timestamp'], (new DateTimeImmutable())->format('H:i:s'));
//                                printf("\t\tTX hash: %s\n", $channelData['hash']);
//                                printf("\t\tTX height: %s\n", $channelData['height']);
//                                printf("\t\tTX from: %s\n", $channelData['from']);
//
//                                if ($sellS === 'BIP' && $buyS === 'BIP') {
//                                    printf(
//                                        "\tRoute: %s\n",
//                                        implode('=>', array_map(static fn(array $item) => $item['symbol'], $coinsData['coins']))
//                                    );
//                                }
////                                printf("Received: %s\n", $payload);
//
//                                $processes = [];
//                                $command = [
//                                    '/var/www/ccbip/bin/console',
//                                    'app:named-pools:arbitrate',
//                                    "--read-node='https://api.minter.one/v2/'",
//                                    "--write-node='https://api.minter.one/v2/'",
//                                    '--req-delay=0',
//                                    '-a 5000 -a 4000 -a 3000 -a 2000',
//                                    '-i 4',
//                                    '-vvv'
//                                ];
//
//                                $runProcess = coroutine(function(Process $process, string $name): iterable {
//                                    $process->start();
//                                    $exitCode = yield $process->join();
//                                    $stdout = trim(yield $process->getStdout()->read());
//                                    return compact('name', 'exitCode', 'stdout');
//                                });
//
//
//                                if (isset($pools[$sellS])) {
//                                    $command[] = "-p $sellS";
//                                    $processes['first'] = new Process(implode(' ', $command));
////
////                                $j = 0;
////                                do {
////                                    (new PoolsArbitrator($this->logger))->arbitrate(
////                                        $pools[$sellS],
////                                        3748,
////                                        new MinterAPI('https://api.minter.one/v2/'),
////                                        new MinterAPI('https://api.minter.one/v2/'),
////                                        0,
////                                        0,
////                                        $wallets
////                                    );
////                                    $j++;
////                                    sleep($j);
////                                } while ($j < 3);
//                                }
//
//                                if (isset($pools[$buyS])) {
//                                    $command[] = "-p $sellS";
//                                    $processes['second'] = new Process(implode(' ', $command));
////
////                                $k = 0;
////                                do {
////                                    (new PoolsArbitrator($this->logger))->arbitrate(
////                                        $pools[$buyS],
////                                        3587,
////                                        new MinterAPI('https://api.minter.one/v2/'),
////                                        new MinterAPI('https://api.minter.one/v2/'),
////                                        0,
////                                        1,
////                                        $wallets
////                                    );
////                                    $k++;
////                                    sleep($k);
////                                } while ($k < 3);
//                                }
//
//                                if ($processes) {
//                                    $outputs = yield all(array_map($runProcess, $processes, array_keys($processes)));
////                                    var_dump($outputs);
//                                    $processes = [];
//                                }
//                            }
//                        }
//
//
//                    } catch (JsonException $e) {
//                    }
//
////                if ($payload === 'Goodbye!') {
////                    $connection->close();
////                    break;
////                }
////
////                yield new Delayed(1000);
////
////                if ($i < 3) {
////                    yield $connection->send('Ping: ' . ++$i);
////                } else {
////                    yield $connection->send('Goodbye!');
////                }
//                }
//            });
//        } catch (ClosedException) {
//            $this->loopRun($pools, $wallets);
//        } catch (DnsException) {
//            $this->loopRun($pools, $wallets);
//        }
//    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
//        $wallet = MinterWallet::createFromMnemonic('');
//        dump($wallet->getPrivateKey());
        $amounts = '';
        foreach ($input->getOption('tx-amounts') as $amount) {
            $amounts .= " -a {$amount}";
        }
        Loop::run(function () use ($amounts, $input) {
            while (true) {
                sleep(1);
                $indexedCoins = (new PoolsStore())->coinsIndexedById();
                try {
                    $txs = (new MinterAPI('https://api.minter.one/v2/'))->getUnconfirmedTxs();
                } catch (RequestException $e) {
                    sleep(1);
                    continue;
                }

//            printf("Found Txs: %s\n", count($txs->transactions));
//            printf("\tBase64: %s\n", base64_encode(json_encode($txs->transactions)));
                $processes = [];
                $runProcess = coroutine(function (Process $process, string $name) : iterable {
                    $process->start();
                    $exitCode = yield $process->join();
                    $stdout = trim(yield $process->getStdout()->read());

                    return compact('name', 'exitCode', 'stdout');
                });

                foreach ($txs->transactions as $tx) {
                    $tx1 = explode('{', $tx);
                    $tx2 = explode('}', $tx1[1]);
                    try {
                        $mtx = MinterTx::decode($tx2[0]);
                    } catch (Throwable $e) {
                        continue;
                    }

                    /** @var MinterSellSwapPoolTx $dt */
                    $dt = $mtx->getData();

                    if (in_array((int) $dt->getType(), [24, 25, 23], true)) {
                        $f = $dt->coins[array_key_first($dt->coins)];
                        $l = $dt->coins[array_key_last($dt->coins)];

                        if ($f !== $l) {
                            printf("Type: %s (%s)\n", $dt->getType(), (new DateTimeImmutable())->format('H:i:s'));
                            printf("\tSender: %s\n", $mtx->getSenderAddress());
                            printf("\tNonce: %s\n", $mtx->getNonce());

                            $coins = [];
                            foreach ($dt->coins as $coinId) {
                                if (isset($indexedCoins[$coinId]) && $coinId > 0) {
                                    $coins[] = $indexedCoins[$coinId]->getSymbol();
                                    $command = [
                                        '/var/www/ccbip/bin/console',
                                        'app:named-pools:arbitrate',
                                        "--read-node='https://api.minter.one/v2/'",
                                        "--write-node='https://api.minter.one/v2/'",
                                        "--req-delay={$input->getOption('req-delay')}",
                                        $amounts,
                                        '-i 4',
                                        "--wallets-file={$input->getOption('wallets-file')}",
                                        '-vvv',
                                    ];
                                    $command[] = "-p {$indexedCoins[$coinId]->getSymbol()}";
                                    $cmd = implode(' ', $command);
                                    printf("\tCMD: %s\n", $cmd);
                                    $processes[] = new Process($cmd);
                                    // Можно попробовать пустить через очередь - beanstalkd

                                }
                            }
                            printf("\tRoute: %s\n", implode('=>', $coins));

//                            if ($coins) {
//                                $cmd = implode(' ', $command);
//                                printf("\tCMD: %s\n", $cmd);
//                                $processes[] = new Process($cmd);
//                            }
                        }
                    }
                }

                if ($processes) {
                    $outputs = yield all(array_map($runProcess, $processes, array_keys($processes)));
                    printf("Finished\n\n");
//                    var_dump($outputs);
                    $processes = [];
                }
            }
        });


        return 0;

        $bip = new CoinDto(0, 'BIP');
        $hub = new CoinDto(1902, 'HUB');
        $rubx = new CoinDto(1784, 'RUBX');
        $monsterHub = new CoinDto(1895, 'MONSTERHUB');
        $liquidHub = new CoinDto(1893, 'LIQUIDHUB');
        $hubChain = new CoinDto(1900, 'HUBCHAIN');
        $cap = new CoinDto(1934, 'CAP');
        $hubabuba = new CoinDto(1942, 'HUBABUBA');
        $bigmac = new CoinDto(907, 'BIGMAC');
        $usdx = new CoinDto(1678, 'USDX');
        $quota = new CoinDto(1086, 'QUOTA');
        $coupon = new CoinDto(1086, 'COUPON');
        $microb = new CoinDto(1087, 'MICROB');
        $ftmusd = new CoinDto(1048, 'FTMUSD');
        $freedom = new CoinDto(21, 'FREEDOM');

        //TODO: Нужно процессить только если оба коина не bip.
        // И в таком случае у нас есть точные роуты. Может быть так будет работать лучше.
        // 2. И нужен какой-то воркер, который будет делать рестарт
        // И нужно делать асинхронную работу в пулах (покупку).


        $pools = [
            'FTMUSD' => [
                [$bip, $ftmusd, $usdx, $bip],
                [$bip, $usdx, $ftmusd, $bip],
                [$bip, $freedom, $ftmusd, $bip],
                [$bip, $ftmusd, $freedom, $bip],
            ],
            'FREEDOM' => [
                [$bip, $freedom, $ftmusd, $bip],
                [$bip, $ftmusd, $freedom, $bip],
            ],
            'MICROB' => [
                [$bip, $microb, $usdx, $bip],
                [$bip, $usdx, $microb, $bip],
            ],
            'COUPON' => [
                [$bip, $bigmac, $coupon, $bip],
                [$bip, $coupon, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
            ],
            'QUOTA' => [
                [$bip, $bigmac, $quota, $bip],
                [$bip, $quota, $bigmac, $bip],
            ],
            'USDX' => [
                [$bip, $bigmac, $usdx, $bip],
                [$bip, $usdx, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
            ],
            'BIGMAC' => [
                [$bip, $bigmac, $coupon, $bip],
                [$bip, $coupon, $bigmac, $bip],
                [$bip, $bigmac, $quota, $bip],
                [$bip, $quota, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $bip],
                [$bip, $usdx, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
            ],
            'HUB' => [
                [$bip, $hub, $rubx, $bip],
                [$bip, $rubx, $hub, $bip],
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
            ],
            'MONSTERHUB' => [
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
            ],
            'HUBCHAIN' => [
                [$bip, $hubChain, $hub, $bip],
                [$bip, $hub, $hubChain, $bip],
            ],
            'LIQUIDHUB' => [
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
            ],
            'CAP' => [
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
            ],
            'HUBABUBA' => [
                [$bip, $hub, $hubabuba, $bip],
                [$bip, $hubabuba, $hub, $bip],
            ],
            'RUBX' => [
                [$bip, $hub, $rubx, $bip],
                [$bip, $rubx, $hub, $bip],
            ],
        ];

        $wallets = [
            0 => [
                'wallet' => 'Mx3d6927d293a446451f050b330aee443029be1564',
                'pk' => '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd',
            ],
            1 => [
                'wallet' => 'Mxa7de32768daa3e3d3273b9e251e424be33858cfa',
                'pk' => '4d09292487ba49d2b53b3d2685d77569341d4e02e4a6fcc3e621556aa37a3677',
            ],
            2 => [
                'wallet' => 'Mx8ab4f4f3909182e1dd5bebf239a043960e4e4557',
                'pk' => '30fbeff069a78b69afe75e2b43459af4674cf915b3799a57bb739f236d151f88',
            ],
            3 => [
                'wallet' => 'Mx7586ad025e0f6665c28528f6844ddd00185d097c',
                'pk' => 'da708822753c1c3f054c1e63d0667fcb7b9e2beb59930880905789c6d82e6025',
            ],
        ];

        $this->loopRun((new PoolsStore())->getPools(), $wallets);

        return 0;
        //Боевая сеть: https://api.minter.stakeholder.space/
        //Тестовая сеть: https://api.testnet.minter.stakeholder.space/

//        $nodeUrl = 'https://node-api.taconet.minter.network/v2/';
//        $nodeUrl = 'https://mnt.funfasy.dev/v2/';
        $nodeUrl = 'https://api.minter.one/v2/';
        $api = new MinterAPI($nodeUrl);


        //        array(5) {
        //        'seed' =>
        //  string(128) "bd1eb0e9937a89f25452b049ecfaa884f32dd2fb56b63c969328bda6b2eb129ba1a534c63fc65cb234256a434b809f5e6436c534a3316f436ee8eeb8809f0f26"
        //  'address' =>
        //  string(42) "Mx3d6927d293a446451f050b330aee443029be1564"
        //  'mnemonic' =>
        //  string(79) "arrange include say lounge knock century unique swim warfare slush raise planet"
        //  'public_key' =>
        //  string(130) "Mp071d479665dd3ece05eb68e9552a23f3bb76096d411a36806eb79d7777d2dded2b96ec9744200c90a16385ecb70f4a534168dbbaebb61f43504b0a2b966921fc"
        //  'private_key' =>
        //  string(64) "76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd"
        //}
        $address = 'Mx3d6927d293a446451f050b330aee443029be1564';
        $privateKey = '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd';
        var_dump(MinterConverter::convertValue('6000000000000000000000', 'bip'));

//        $nodeUrl = 'https://minter-node-1.testnet.minter.network:8841'; // example of a node url
        $nodeUrl = 'https://api.minter.stakeholder.space/'; // example of a node url
//        $nodeUrl = 'https://api.minter.stakeholder.space/';
        $api = new MinterAPI($nodeUrl);
//        $wallet = MinterWallet::create();
//        var_dump($wallet);

        $items = [
            "achieve",
            "addict",
            "album",
            "allow",
            "angry",
            "april",
            "arrow",
            "auction",
            "bachelor",
            "base",
            "beyond",
            "blind",
            "boil",
            "bright",
            "buffalo",
            "busy",
            "capable",
            "case",
            "chair",
            "cherry",
            "city",
            "clever",
            "combine",
            "conduct",
            "craft",
            "crane",
            "cruel",
            "cycle",
            "deer",
            "desk",
            "devote",
            "dish",
            "donor",
            "dream",
            "dust",
            "elephant",
            "emotion",
            "equal",
            "erosion",
            "exit",
            "express",
            "father",
            "festival",
            "fire",
            "flock",
            "force",
            "frost",
            "gas",
            "gentle",
            "give",
            "goose",
            "grid",
            "hair",
            "high",
            "hockey",
            "hunt",
            "impact",
            "infant",
            "inside",
            "island",
            "junk",
            "kite",
            "labor",
            "legal",
            "link",
            "lobster",
            "lunch",
            "manage",
            "maze",
            "mesh",
            "minimum",
            "mosquito",
            "move",
            "need",
            "never",
            "notable",
            "old",
            "olympic",
            "order",
            "ozone",
            "patch",
            "pelican",
            "piece",
            "please",
            "present",
            "project",
            "public",
            "quote",
            "radio",
            "raw",
            "remove",
            "require",
            "ribbon",
            "roof",
            "route",
            "sample",
            "science",
            "segment",
            "session",
            "shrug",
            "similar",
            "skill",
            "sniff",
            "solution",
            "space",
            "stable",
            "stamp",
            "struggle",
            "sunny",
            "swing",
            "tail",
            "taxi",
            "tide",
            "timber",
            "tone",
            "train",
            "truck",
            "turn",
            "umbrella",
            "until",
            "usual",
            "venue",
            "vocal",
            "wait",
            "weekend",
            "where",
            "worth",
            "yellow",
        ];

        foreach ($items as $key => $item) {
            $output->writeln("{$key}: {$item}");
            $mnemonic = "canvas people cram lobster journey cream vocal damp annual blade bind {$item}";
            $seed = MinterWallet::mnemonicToSeed($mnemonic);
            $privateKey = MinterWallet::seedToPrivateKey($seed);
            $publicKey = MinterWallet::privateToPublic($privateKey);
            $address = MinterWallet::getAddressFromPublicKey($publicKey);
            $balance = $api->getBalance($address);
            if ($balance->result->transaction_count) {
                $output->writeln('************************************');
                $output->writeln($mnemonic);
                $output->writeln($address);
                var_dump($balance);
                $output->writeln('************************************');
            }

            sleep(5);
        }


//        var_dump($api->getValidators());
        exit();
//        var_dump($api->getStatus());
//        var_dump($api->estimateCoinBuy('MNT', 1, 'MNT'));

        $coinSymbol = '9OOOOOOOO5';
        $mntSymbol = 'MNT';
        $output->writeln("CREATE {$coinSymbol}");
        $txCreate = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterCreateCoinTx::TYPE,
            'data' => [
                'name' => 'Test Coin',
                'symbol' => $coinSymbol,
                'initialAmount' => '1000',
                'initialReserve' => '1000',
                'crr' => 40,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);
//        try {
//            $response = $api->send($txCreate->sign($privateKey));
//            print_r($response);
//        } catch(RequestException $exception) {
//            var_dump($exception->getResponse()->getBody()->getContents());
//        }

        // BUY COIN
        foreach ([100, 50, 100] as $qty) {
            $output->writeln("BUY {$qty}");
            sleep(10);
            $txBuy = new MinterTx([
                'nonce' => $api->getNonce($address),
                'chainId' => MinterTx::TESTNET_CHAIN_ID,
                'gasPrice' => 1,
                'gasCoin' => $mntSymbol,
                'type' => MinterBuyCoinTx::TYPE,
                'data' => [
                    'coinToBuy' => $coinSymbol,
                    'valueToBuy' => "{$qty}",
                    'coinToSell' => $mntSymbol,
                    'maximumValueToSell' => 2000,
                ],
                'payload' => '',
                'serviceData' => '',
                'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE,
            ]);

//            try {
//                $response = $api->send($txBuy->sign($privateKey));
//            $response = $api->getTransaction('MtD0589C9ADA5CDCE7C21B60FD684BB04AF067261737749B6E18A6B2168A348B89');
//                print_r($response);
//            } catch(RequestException $exception) {
//                var_dump($exception->getResponse()->getBody()->getContents());
//                // handle error
//            }
        }

        sleep(60);
        $output->writeln('SELL 500');
        $txSell = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterSellCoinTx::TYPE,
            'data' => [
                'coinToSell' => $coinSymbol,
                'valueToSell' => '500',
                'coinToBuy' => $mntSymbol,
                'minimumValueToBuy' => 1,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);

//        try {
//            $response = $api->send($txSell->sign($privateKey));
//            print_r($response);
//        } catch (RequestException $exception) {
//            var_dump($exception->getResponse()->getBody()->getContents());
//        }

        //SELL ALL
        $output->writeln('SELL ALL');
        $txSellAll = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterSellAllCoinTx::TYPE,
            'data' => [
                'coinToSell' => $coinSymbol,
                'coinToBuy' => $mntSymbol,
                'minimumValueToBuy' => 1,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);

        try {
            $response = $api->send($txSellAll->sign($privateKey));
//            $response = $api->getTransaction("Mt{$response->result->hash}");
            print_r($response);
        } catch (RequestException $exception) {
            var_dump($exception->getResponse()->getBody()->getContents());
            // handle error
        }
    }
}
