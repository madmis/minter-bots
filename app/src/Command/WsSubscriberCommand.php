<?php

namespace App\Command;

use Amp\Dns\DnsException;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Websocket\Client\Connection;
use Amp\Websocket\ClosedException;
use Amp\Websocket\Message;
use App\Services\PoolsStore;
use DateTimeImmutable;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Amp\coroutine;
use function Amp\Promise\all;
use function Amp\Websocket\Client\connect;

/**
 * Class WsSubscriberCommand
 * @package App\Command
 */
class WsSubscriberCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:ws:subscribe';

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
            ->addOption('tx-amounts', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Transaction amounts', [5000, 4000, 3000])
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
        $amounts = '';
        foreach ($input->getOption('tx-amounts') as $amount) {
            $amounts .= " -a {$amount}";
        }

        try {
            Loop::run(function () use ($pools, $input, $amounts) {
                /** @var Connection $connection */
                $connection = yield connect('wss://explorer-rtm.minter.network/connection/websocket');
                yield $connection->send('{"id":1}');

                /** @var Message $message */
                while ($message = yield $connection->receive()) {
                    $payload = yield $message->buffer();

                    try {
                        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

                        if (isset($data['id']) && $data['id'] === 1) {
                            yield $connection->send('{"method":1,"params":{"channel":"transactions_100"},"id":3}');
                        }

                        if (isset($data['result']['channel']) && $data['result']['channel'] === 'transactions_100') {
                            $channelData = $data['result']['data']['data'];
                            // Types
                            // 4 MinterBuyCoinTx, 2 MinterSellCoinTx, 3 MinterSellAllCoinTx,
                            // 24 MinterBuySwapPoolTx, 23 MinterSellSwapPoolTx, 25 MinterSellAllSwapPoolTx
                            // Нам нужны только транзакции в пулах
                            if (in_array((int) $channelData['type'], [24, 25, 23], true)) {
                                $coinsData = $channelData['data'];
                                $sellS = $coinsData['coin_to_sell']['symbol'];
                                $buyS = $coinsData['coin_to_buy']['symbol'];

                                printf("Type: %s\n", $channelData['type']);
                                printf("\tBuy: %s\n", $coinsData['coin_to_buy']['symbol']);
                                printf("\tSell: %s\n", $coinsData['coin_to_sell']['symbol']);
                                printf("\t\tTX time - Local time: %s - %s\n", $channelData['timestamp'], (new DateTimeImmutable())->format('H:i:s'));
                                printf("\t\tTX hash: %s\n", $channelData['hash']);
                                printf("\t\tTX height: %s\n", $channelData['height']);
                                printf("\t\tTX from: %s\n", $channelData['from']);

                                if ($sellS === 'BIP' && $buyS === 'BIP') {
                                    printf(
                                        "\tRoute: %s\n",
                                        implode('=>', array_map(static fn(array $item
                                        ) => $item['symbol'], $coinsData['coins']))
                                    );
                                }
//                                printf("Received: %s\n", $payload);

                                $processes = [];

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

                                $runProcess = coroutine(function (Process $process, string $name) : iterable {
                                    $process->start();
                                    $exitCode = yield $process->join();
                                    $stdout = trim(yield $process->getStdout()->read());

                                    return compact('name', 'exitCode', 'stdout');
                                });


                                if (isset($pools[$sellS])) {
                                    $command[] = "-p $sellS";
                                    $processes['first'] = new Process(implode(' ', $command));

//                                    printf("Run command first: %s\n", implode(' ', $command));

                                }

                                if (isset($pools[$buyS])) {
                                    $command[] = "-p $sellS";
                                    $processes['second'] = new Process(implode(' ', $command));
//                                    printf("Run command second: %s\n", implode(' ', $command));
                                }

                                if ($processes) {
                                    $outputs = yield all(array_map($runProcess, $processes, array_keys($processes)));
//                                    var_dump($outputs);
                                    $processes = [];
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
}
