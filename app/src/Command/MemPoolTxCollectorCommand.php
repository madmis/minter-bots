<?php

namespace App\Command;

use App\Dto\Messenger\PredefinedRoutesMessage;
use App\Services\PoolsStore;
use DateTimeImmutable;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterTx;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Throwable;

/**
 * Class MemPoolTxCollectorCommand
 * @package App\Command
 */
class MemPoolTxCollectorCommand extends Command
{
    protected static $defaultName = 'app:mempool:tx:collector';

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

        while (true) {
            try {
                $txs = (new MinterAPI('https://api.minter.one/v2/'))->getUnconfirmedTxs();
            } catch (RequestException|ConnectException) {
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

                    /** @var MinterSellSwapPoolTx $dt */
                    $txData = $mtx->getData();

                    if (in_array($txData->getType(), [24, 25, 23], true)) {
                        $first = $txData->coins[array_key_first($txData->coins)];
                        $last = $txData->coins[array_key_last($txData->coins)];

                        if ($first !== $last) {
                            printf("Type: %s (%s)\n", $txData->getType(), (new DateTimeImmutable())->format('H:i:s'));
                            printf("\tSender: %s\n", $mtx->getSenderAddress());
//                            printf("\tNonce: %s\n", $mtx->getNonce());

                            $coins = [];
                            foreach ($txData->coins as $coinId) {
                                if (isset($indexedCoins[$coinId]) && $coinId > 0) {
                                    $coins[$coinId] = $indexedCoins[$coinId]->getSymbol();
                                }
                            }

                            if ($coins) {
                                $routes = [];
                                // TODO: собирать роуты сразу из коинов на месте компонуя их с BIP на входе и выходе
                                foreach ($coins as $coinId1 => $coin1) {
                                    foreach ($coins as $coinId2 => $coin2) {
                                        if ($coinId1 !== $coinId2) {
                                            $routes[] = [0, $coinId1, $coinId2, 0];
                                        }
                                    }
                                }
                                $this->bus->dispatch(new PredefinedRoutesMessage($routes));
                                $this->bus->dispatch(new PredefinedRoutesMessage($routes), [new DelayStamp(3000)]);
                                $this->bus->dispatch(new PredefinedRoutesMessage($routes), [new DelayStamp(6000)]);

//                                foreach ($coins as $coinId => $coin) {
//                                    $this->bus->dispatch(new OneCoinMessage($coinId));
//                                }
                                printf("\tRoute: %s\n", implode('=>', $coins));
                            }
                        }
                    }
                }
            }
            
            if (count($txs->transactions) > 0) {
                sleep(1);
            } else {
                usleep(200000);
            }
        }

        return self::SUCCESS;
    }
}
