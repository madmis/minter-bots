<?php /** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Command;

use App\Dto\CoinDto;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Minter\SDK\MinterWallet;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class ArbitrateUSDCommand
 * @package App\Command
 */
class ArbitrateUSDCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:usd:arbitrate';

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallet = [
        'wallet' => 'Mxa7de32768daa3e3d3273b9e251e424be33858cfa',
        'pk' => '4d09292487ba49d2b53b3d2685d77569341d4e02e4a6fcc3e621556aa37a3677',
    ];

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

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Collect auto pool and arbitrate in Minter pools.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $usdte = new CoinDto(1993, 'USDTE');
        $usdce = new CoinDto(1994, 'USDCE');
        $musd = new CoinDto(2024, 'MUSD');
        $hub = new CoinDto(1902, 'HUB');
        $hubabuba = new CoinDto(1942, 'HUBABUBA');
        $usdx = new CoinDto(1678, 'USDX');
        $bigmac = new CoinDto(907, 'BIGMAC');
        $quota = new CoinDto(1086, 'QUOTA');
        $coupon = new CoinDto(1043, 'COUPON');
        $microb = new CoinDto(1087, 'MICROB');
        $yankee = new CoinDto(1074, 'YANKEE');
        $oracul = new CoinDto(1084, 'ORACUL');

        $routes = [
            1993 => [
                [$usdte, $usdce],
                [$usdte, $musd],
                [$usdte, $usdx],
                [$usdte, $musd, $usdce],
                [$usdte, $usdce, $musd],
                [$usdte, $hub, $musd],
                [$usdte, $hub, $usdce],
                [$usdte, $hub, $usdce, $musd],
                [$usdte, $hub, $musd, $usdce],
                [$usdte, $hubabuba, $hub, $musd],
                [$usdte, $hubabuba, $hub, $usdce],
            ],
            1994 => [
                [$usdce, $usdte],
                [$usdce, $musd],
                [$usdce, $usdx],
                [$usdce, $musd, $usdte],
                [$usdce, $usdte, $musd],
                [$usdce, $hub, $usdte],
                [$usdce, $hub, $musd],
                [$usdce, $hub, $usdte, $musd],
                [$usdce, $hub, $hubabuba, $usdte, $musd],
                [$usdce, $hub, $musd, $usdte],
            ],
            2024 => [
                [$musd, $usdte],
                [$musd, $usdce],
                [$musd, $usdx],
                [$musd, $usdce, $usdte],
                [$musd, $usdte, $usdce],
                [$musd, $hub, $usdce],
                [$musd, $hub, $usdte],
                [$musd, $hub, $usdte, $usdce],
                [$musd, $hub, $hubabuba, $usdte, $usdce],
                [$musd, $hub, $usdce, $usdte],
            ],
            1678 => [
                [$usdx, $usdte],
                [$usdx, $usdte, $usdce],
                [$usdx, $usdte, $musd],
                [$usdx, $usdce],
                [$usdx, $usdce, $usdte],
                [$usdx, $usdce, $musd],
                [$usdx, $musd],
                [$usdx, $musd, $usdce],
                [$usdx, $musd, $usdte],
                [$usdx, $hub, $usdce],
                [$usdx, $hub, $usdte],
                [$usdx, $hub, $musd],
                [$usdx, $hub, $usdce, $usdte],
                [$usdx, $hub, $usdce, $musd],
                [$usdx, $hub, $usdte, $usdce],
                [$usdx, $hub, $usdte, $musd],
                [$usdx, $hub, $musd, $usdce],
                [$usdx, $hub, $musd, $usdte],
                [$usdx, $hub, $hubabuba, $usdte],
                [$usdx, $hub, $hubabuba, $usdte, $usdce],
                [$usdx, $hub, $hubabuba, $usdte, $musd],
            ],
        ];
        $api = new MinterAPI('https://api.minter.one/v2/');
        $balance = $this->getBalance($api, $this->logger, true);
        $reqDelay = 1000000;

        while (true) {
            foreach ($balance->balance as $coinData) {
                if (isset($routes[$coinData->coin->id])) {
                    $amount = (float) MinterConverter::convertToBase($coinData->value);
                    $amount -= 0.01;

                    if ($amount > 1) {
                        foreach ($routes[$coinData->coin->id] as $route) {
                            try {
                                $r = implode('=>', array_map(
                                    static fn(CoinDto $coin) => $coin->getSymbol(),
                                    $route,
                                ));

                                try {
                                    $this->logger->debug("R: {$r}");

                                    $data = new MinterSellSwapPoolTx(
                                        array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
                                        $amount,
                                        $amount + 0.02
                                    );
                                    $nonce = $api->getNonce($this->wallet['wallet']);
                                    $tx = new MinterTx($nonce, $data);
                                    $signedTx = $tx->sign($this->wallet['pk']);
                                    $response = $api->send($signedTx);
                                    $this->logger->info("R: {$r}");
                                    $this->logger->info("\t", ['response' => (array) $response]);
                                    sleep(5);
                                    $balance = $this->getBalance($api, $this->logger, true);

                                    break 2;
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
                                    } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 107) {
                                        $this->logger->info("Insufficient funds for sender account R: {$r}");
                                        $balance = $this->getBalance($api, $this->logger, true);
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
                                sleep(2);
                            } catch (GuzzleException $e) {
                                sleep(3);
                            }
                        }
                    }
                }
            }
        }

        return self::SUCCESS;
    }


    /**
     * getBalance.
     *
     * @param MinterAPI $api
     * @param LoggerInterface $logger
     * @param bool $print
     *
     * @return stdClass
     */
    private function getBalance(MinterApi $api, LoggerInterface $logger, bool $print) : stdClass
    {
        $fnPrint = static function (stdClass $balance) use ($logger) {
            $logger->info('Balance');
            foreach ($balance->balance as $coinData) {
                $value = MinterConverter::convertToBase($coinData->value);
                $logger->info("\t{$coinData->coin->symbol}: {$value}");
            }
        };

        try {
            $balance = $api->getBalance($this->wallet['wallet']);
            if ($print) {
                $fnPrint($balance);
            }
        } catch (Throwable) {
            sleep(2);
            try {
                $balance = $api->getBalance($this->wallet['wallet']);
                if ($print) {
                    $fnPrint($balance);
                }
            } catch (Throwable) {
                sleep(2);
                $balance = $api->getBalance($this->wallet['wallet']);
                if ($print) {
                    $fnPrint($balance);
                }
            }
        }

        return $balance;
    }
}
