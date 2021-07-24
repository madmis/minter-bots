<?php /** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Command;

use App\Dto\CoinDto;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class ArbitrateLhubMHubCommand
 * @package App\Command
 */
class ArbitrateLhubMHubCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:lhub-mhub:arbitrate';

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallet = [
        'wallet' => 'Mx4e4a7d12299380658939ff690098c2e6ddae2889',
        'pk' => 'e11846ace8b5b3a475ae6c4f944a90baa82b50805e9c315a3bce0e7c678b3bb8',
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
        $monsterHub = new CoinDto(1895, 'MONSTERHUB');
        $liquidHub = new CoinDto(1893, 'LIQUIDHUB');
        $hub = new CoinDto(1902, 'HUB');
        $hubChain = new CoinDto(1900, 'HUBCHAIN');
        $moneHub = new CoinDto(1901, 'MONEHUB');
        $oracul = new CoinDto(1084, 'ORACUL');
        $bip = new CoinDto(0, 'BIP');
        $usdx = new CoinDto(1678, 'USDX');
        $usdte = new CoinDto(1993, 'USDTE');
        $usdce = new CoinDto(1994, 'USDCE');
        $musd = new CoinDto(2024, 'MUSD');

        $routes = [
            1084 => [
                [$oracul, $liquidHub],
                [$oracul, $bip, $liquidHub],
                [$oracul, $bip, $hub, $liquidHub],
                [$oracul, $usdx, $bip, $liquidHub],
                [$oracul, $bip, $usdx, $liquidHub],
                [$oracul, $usdx, $usdte, $bip, $liquidHub],
                [$oracul, $usdx, $usdce, $bip, $liquidHub],
                [$oracul, $usdx, $musd, $bip, $liquidHub],

                [$oracul, $monsterHub],
                [$oracul, $bip, $monsterHub],
                [$oracul, $bip, $hub, $monsterHub],
                [$oracul, $usdx, $bip, $monsterHub],
                [$oracul, $bip, $usdx, $monsterHub],
                [$oracul, $usdx, $usdte, $bip, $monsterHub],
                [$oracul, $usdx, $usdce, $bip, $monsterHub],
                [$oracul, $usdx, $musd, $bip, $monsterHub],

                [$oracul, $moneHub],
                [$oracul, $bip, $moneHub],
                [$oracul, $bip, $hub, $moneHub],
                [$oracul, $usdx, $bip, $moneHub],
                [$oracul, $bip, $usdx, $moneHub],
                [$oracul, $usdx, $usdte, $bip, $moneHub],
                [$oracul, $usdx, $usdce, $bip, $moneHub],
                [$oracul, $usdx, $musd, $bip, $moneHub],
            ],
            1901 => [
                [$moneHub, $liquidHub],
                [$moneHub, $bip, $liquidHub],
                [$moneHub, $hub, $liquidHub],
                [$moneHub, $hub, $monsterHub],
                [$moneHub, $oracul],
                [$moneHub, $oracul, $liquidHub],
                [$moneHub, $oracul, $monsterHub],
                [$moneHub, $bip, $oracul],
                [$moneHub, $bip, $monsterHub],
                [$moneHub, $bip, $liquidHub],
            ],
            1895 => [
                [$monsterHub, $liquidHub],
                [$monsterHub, $oracul],
                [$monsterHub, $bip, $oracul],
                [$monsterHub, $bip, $liquidHub],
                [$monsterHub, $oracul, $liquidHub],
                [$monsterHub, $liquidHub, $oracul,],
                [$monsterHub, $hub, $bip, $liquidHub],
                [$monsterHub, $hub, $liquidHub],
                [$monsterHub, $hub, $moneHub, $liquidHub],
                [$monsterHub, $hub, $hubChain, $liquidHub],
                [$monsterHub, $hub, $moneHub],
                [$monsterHub, $bip, $moneHub],
                [$monsterHub, $oracul, $moneHub],
            ],
            1893 => [
                [$liquidHub, $monsterHub],
                [$liquidHub, $bip, $monsterHub],
                [$liquidHub, $bip, $moneHub],
                [$liquidHub, $oracul],
                [$liquidHub, $oracul, $monsterHub],
                [$liquidHub, $oracul, $moneHub],
                [$liquidHub, $bip, $oracul],
                [$liquidHub, $hub, $monsterHub],
                [$liquidHub, $moneHub, $hub, $monsterHub],
                [$liquidHub, $hubChain, $hub, $monsterHub],
            ],
        ];
        $api = new MinterAPI('https://api.minter.one/v2/');
        $balance = $this->getBalance($api, $this->logger, true);
        $reqDelay = 1000000;
        $fee = 0.01;
        $minGasPrice = 2;

        while (true) {
            $balance = $this->getBalance($api, $this->logger, false);

            foreach ($balance->balance as $coinData) {
                if (isset($routes[$coinData->coin->id])) {
                    $amount = (float) MinterConverter::convertToBase($coinData->value);
                    $amount -= 0.001;

                    if ($amount > 0.1) {
                        foreach ($routes[$coinData->coin->id] as $route) {
                            try {
                                $r = implode('=>', array_map(
                                    static fn(CoinDto $coin) => $coin->getSymbol(),
                                    $route,
                                ));
//
//                                try {
//                                    $minGasPrice = (int)$api->getMinGasPrice()->min_gas_price;
//                                    $this->logger->info("Min GAS Price: {$minGasPrice}");
//                                } catch (Throwable $e) {
//                                    dump($e->getMessage());
//                                    sleep(1);
//                                    continue;
//                                }

                                try {
                                    $this->logger->debug("R: {$r}");

                                    $minAmountToBuy = $amount + ($fee * $minGasPrice);
                                    $data = new MinterSellSwapPoolTx(
                                        array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
                                        $amount,
                                        $minAmountToBuy
                                    );
                                    $nonce = $api->getNonce($this->wallet['wallet']);
                                    $tx = (new MinterTx($nonce, $data))->setGasPrice($minGasPrice);
                                    $signedTx = $tx->sign($this->wallet['pk']);
                                    $response = $api->send($signedTx);
                                    $this->logger->info("R: {$r}");
                                    $this->logger->info("\t Amount: {$amount}. Min Buy Amount {$minAmountToBuy}. Gas Price: {$minGasPrice}");
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
                                    } elseif (!empty($data['error']['code']) && (int) $data['error']['code'] === 114) {
                                        $this->logger->info($data['error']['message'], $data['error']['data']);
                                        break 2;
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

        $balance = null;

        do {
            try {
                $balance = $api->getBalance($this->wallet['wallet']);
                if ($print) {
                    $fnPrint($balance);
                }
            } catch (Throwable) {
                sleep(2);
            }
        } while (!$balance);

        return $balance;
//
//        try {
//            $balance = $api->getBalance($this->wallet['wallet']);
//            if ($print) {
//                $fnPrint($balance);
//            }
//        } catch (Throwable) {
//            sleep(2);
//            try {
//                $balance = $api->getBalance($this->wallet['wallet']);
//                if ($print) {
//                    $fnPrint($balance);
//                }
//            } catch (Throwable) {
//                sleep(2);
//                $balance = $api->getBalance($this->wallet['wallet']);
//                if ($print) {
//                    $fnPrint($balance);
//                }
//            }
//        }
//
//        return $balance;
    }
}
