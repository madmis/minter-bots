<?php

namespace App\Command;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Loop;
use Amp\Promise;
use Amp\Websocket\Client\Connection;
use App\Services\PoolsStore;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function Amp\Websocket\Client\connect;

/**
 * Class PubSubTestCommand
 * @package App\Command
 */
class PubSubTestCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:pubsub:test';

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

    protected function configure() : void
    {
        $this
            ->setDescription('PUB/SUB Test command.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $fnSearchKey = static function (array $events, string $wantedKey) : ?array {
            foreach ($events as $event) {
                if ($event['key'] === $wantedKey) {
                    return $event;
                }
            }

            return null;
        };
        $idxCoins = (new PoolsStore())->coinsIndexedById();

        while (true) {
            printf('Restart stream');

            Loop::run(function () use ($fnSearchKey, $idxCoins) {

                try {
                    $client = HttpClientBuilder::buildDefault();
                    //https://gate-api.minter.network/api/v2/
                    //https://api.minter.one/v2/
                    $request = new \Amp\Http\Client\Request("https://api.minter.one/v2/subscribe?query=tm.event%20%3D%20'Tx'");
                    $request->setHeader('Accept', 'application/json');
                    $request->setInactivityTimeout(100000);
                    $request->setTransferTimeout(100000);
                    $response = yield $client->request($request);

                    $body = $response->getBody();
                    while (null !== $chunk = yield $body->read()) {
                        $chunks = explode(PHP_EOL, trim($chunk));
                        printf("Chunk: %s\n\n", $chunk);

                        foreach ($chunks as $message) {
                            try {
                                $data = json_decode($message, true, 512, JSON_THROW_ON_ERROR);
                                //TypeSellSwapPool	0x17
                                //TypeBuySwapPool	0x18
                                //TypeSellAllSwapPool	0x19
                                $types = ['17', '18', '19'];

                                if (!empty($data['result']['events'])) {
                                    $typeEvent = $fnSearchKey($data['result']['events'], 'tags.tx.type');
                                    dump($typeEvent);

                                    if ($typeEvent && isset($typeEvent['events'][0]) && in_array($typeEvent['events'][0], $types, false)) {
                                        $poolsEvent = $fnSearchKey($data['result']['events'], 'tags.tx.pools');
                                        printf("Tx TYPE: %s\n", $typeEvent['events'][0]);
                                        printf("\t\tLocal time: %s\n", (new DateTimeImmutable())->format('H:i:s'));

                                        dump($poolsEvent);
                                        if (!empty($poolsEvent['events'][0])) {
                                            $pools = json_decode($poolsEvent['events'][0], true, 512, JSON_THROW_ON_ERROR);
                                            dump($poolsEvent);
                                            $coinIn = isset($idxCoins[$pools[0]['coin_in']]) ? $idxCoins[$pools[0]['coin_in']]->getSymbol() : "UNDEF";
                                            $coinOut = isset($idxCoins[$pools[0]['coin_out']]) ? $idxCoins[$pools[0]['coin_out']]->getSymbol() : "UNDEF";

                                            printf(
                                                "Pools id: %s, IN: %s (%s), OUT: %s (%s)\n\n",
                                                $pools[0]['pool_id'],
                                                $coinIn,
                                                $pools[0]['coin_in'],
                                                $coinOut,
                                                $pools[0]['coin_out']
                                            );
                                        }
                                        break;
                                    }
                                }

                            } catch (JsonException $e) {
                                printf("Invalid json: %s\n\n", $message);
                            }
                        }

//                        dump($chunks);

//                        printf("Chunk: %s\n\n", $chunk);
                    }
                } catch (Throwable $e) {
                    printf("Exception: %s, %s\n\n\n", $e::class, $e->getMessage());
                }
            });
        }

        return self::SUCCESS;
    }
}
