<?php

namespace App\Command;

use Amp\Dns\DnsException;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Websocket\Client\Connection;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use App\Services\PoolsArbitrator;
use App\Services\PoolsStore;
use DateTimeImmutable;
use GuzzleHttp\Client;
use JsonException;
use Minter\MinterAPI;
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
 * Class WsBlockSubscriberCommand
 * @package App\Command
 */
class WsBlockSubscriberCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:ws:block:subscribe';


    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * client.
     *
     * @var Client
     */
    private Client $client;

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
        $this->client = new Client(['base_uri' => 'https://explorer-api.minter.network']);
    }

    /**
     * configure.
     *
     * @return void
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Subscribe to minter websocket.')
            ->addOption('req-delay', 'd', InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 200000)
            ->addOption(
                'tx-amounts',
                'a',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Transaction amounts',
                [7551, 7451, 7351]
            )
            ->addOption(
                'wallets-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to JSON wallets file',
                '/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json'
            );

    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->loopRun((new PoolsStore())->getPools(), $input);

        return self::SUCCESS;
    }

    /**
     * loopRun.
     *
     * @param array $pools
     *
     * @return void
     */
    private function loopRun(array $pools, InputInterface $input) : void
    {
        try {
            Loop::run(function () use ($input) {
                /** @var Connection $connection */
                $connection = yield connect('wss://explorer-rtm.minter.network/connection/websocket');
                yield $connection->send('{"id":1}');

                /** @var Message $message */
                while ($message = yield $connection->receive()) {
                    $payload = yield $message->buffer();

                    try {
                        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

                        if (isset($data['id']) && $data['id'] === 1) {
                            yield $connection->send('{"method":1,"params":{"channel":"blocks"},"id":2}');
                        }

                        if (isset($data['result']['channel']) && $data['result']['channel'] === 'blocks') {
                            $channelData = $data['result']['data']['data'];
//                            printf("Block: %s\n", $channelData['height']);
//                            printf("\tTime - Local time: %s - %s\n", $channelData['timestamp'], (new DateTimeImmutable())->format('H:i:s'));
//                            printf("\tTx count: %s\n", $channelData['transaction_count']);

                            if ($channelData['transaction_count']) {
                                try {
                                    $this->processTransactions($channelData['height'], $input);
                                } catch (Throwable $e) {

                                }
                            }
                        }
                    } catch (JsonException $e) {
                    }
                }
            });
        } catch (ClosedException) {
            $this->loopRun($pools, $input);
        } catch (DnsException) {
            $this->loopRun($pools, $input);
        }
    }

    /**
     * processTransactions.
     *
     * @param int $block
     * @param InputInterface $input
     *
     * @return void
     */
    private function processTransactions(int $block, InputInterface $input) : void
    {
        $response = $this->client->get("/api/v2/blocks/{$block}/transactions");
        try {
            $resp = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            foreach ($resp['data'] as $tx) {
                if (in_array((int) $tx['type'], [24, 25, 23], true)) {
//                    printf("Type: %s\n", $tx['type']);
                    $coins = [];

                    foreach ($tx['data']['coins'] as $coin) {
                        if ($coin['symbol'] !== 'BIP') {
                            $coins[] = $coin['symbol'];
                        }
                    }
//                    dump($tx);
//                    dump($tx['coins']);

                    if ($coins) {
                        printf("\tCoins: %s\n", implode(', ', $coins));

                        $readApi = new MinterAPI('https://api.minter.one/v2/');
                        $writeApi = new MinterAPI('https://api.minter.one/v2/');
                        $walletsFile = $input->getOption('wallets-file');
                        $txAmounts = $input->getOption('tx-amounts');
                        $arbitrator = new PoolsArbitrator($this->logger);
                        $poolsDef = (new PoolsStore())->getPools();
                        $routes = [];
                        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));

                        foreach ($coins as $pool) {
                            if (isset($poolsDef[$pool])) {
                                /** @noinspection SlowArrayOperationsInLoopInspection */
                                $routes = array_merge($routes, $poolsDef[$pool]);
                            }
                        }

                        $walletIdx = array_rand($wallets);
                        $arbitrator->arbitrate(
                            $routes,
                            array_rand(array_flip($txAmounts)),
                            $readApi,
                            $writeApi,
                            0,
                            $walletIdx,
                            $wallets,
                        );
                    }
                }
            }
        } catch (JsonException $e) {
        }
    }
}
