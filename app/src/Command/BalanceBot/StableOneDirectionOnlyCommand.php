<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\Command\BalanceBot;

use App\Services\PoolsStore;
use GuzzleHttp\Exception\{ClientException, GuzzleException, ServerException};
use Minter\MinterAPI;
use Minter\SDK\{MinterCoins\MinterSellSwapPoolTx, MinterConverter, MinterTx};
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class StableOneDirectionOnlyCommand
 * @package App\Command\BalanceBot
 */
class StableOneDirectionOnlyCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:stable:one-direction:arbitrate';

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
            ->setDescription('Arbitrate stables one direction, like: LIQUIDHUB=>MONSTERHUB. Working with coins on balance')
            ->addOption('route', null, InputOption::VALUE_REQUIRED, 'Route to trade: LIQUIDHUB=>MONSTERHUB', '')
            ->addOption('trade-amount', null, InputOption::VALUE_REQUIRED, 'Trade amount', 100)
            ->addOption(
                'get-amount',
                null,
                InputOption::VALUE_REQUIRED,
                'Get amount. Its required for example for route MICROB=>BTC. Sell 100 MICROB, Get 0.000100 BTC (margin will be added to this amount)',
                null
            )
            ->addOption('min-margin', null, InputOption::VALUE_REQUIRED, 'Minimum margin amount', 1)
            ->addOption(
                'wallets-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to JSON wallets file',
                null
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $allCoins = (new PoolsStore())->allCoins();
        $tRoute = $input->getOption('route');
        [$first, $second] = explode('=>', $tRoute);
        $firstId = $allCoins[$first]->getId();
        $secondId = $allCoins[$second]->getId();

        $api = new MinterAPI('https://api.minter.one/v2/');
        $walletsFile = $input->getOption('wallets-file');
        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));
        $walletIdx = array_rand($wallets);
        $tradeAmount = $input->getOption('trade-amount');
        $getAmount = $input->getOption('get-amount') ?? $tradeAmount;
        $minMargin = $input->getOption('min-margin');
        $wallet = $wallets[$walletIdx];
        $balance = $this->getBalance($api, $this->logger, false, $wallet);
        $reqDelay = 0;
        $minGasPrice = 2;

        while (true) {
            $coinBalance = $this->findCoinOnBalance($balance, $firstId);
            $amount = (float) MinterConverter::convertToBase($coinBalance->value);

            if ($amount >= $tradeAmount) {

                try {
                    try {
                        $this->logger->debug("R: {$tRoute}");
                        $minAmountToBuy = bcadd((string)$getAmount, (string)$minMargin, 8);
                        $this->logger->debug("minAmountToBuy: {$minAmountToBuy}");
                        $data = new MinterSellSwapPoolTx(
                            [$firstId, $secondId],
                            (string)$tradeAmount,
                            $minAmountToBuy
                        );
                        $nonce = $api->getNonce($wallet['wallet']);
                        $tx = (new MinterTx($nonce, $data))->setGasPrice($minGasPrice);
                        $signedTx = $tx->sign($wallet['pk']);
                        $response = $api->send($signedTx);
                        $this->logger->info("R: {$tRoute}");
                        $this->logger->info("\t Amount: {$tradeAmount}. Min Buy Amount {$minAmountToBuy}. Gas Price: {$minGasPrice}");
                        $this->logger->info("\t", ['response' => (array) $response]);
                        sleep(5);
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
                            $this->logger->info("Insufficient funds for sender account R: {$tRoute}");
                            $balance = $this->getBalance($api, $this->logger, false, $wallet);
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
                        usleep($reqDelay);
                    }
                } catch (ServerException $e) {
                    sleep(2);
                } catch (GuzzleException $e) {
                    sleep(3);
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * findCoinOnBalance.
     *
     * @param stdClass $balance
     * @param int $coinId
     *
     * @return stdClass
     */
    private function findCoinOnBalance(stdClass $balance, int $coinId) : stdClass
    {
        foreach ($balance->balance as $coinData) {
            if ($coinData->coin->id == $coinId) {
                return $coinData;
            }
        }

        throw new \RuntimeException('Unable find coin balance');
    }


    /**
     * getBalance.
     *
     * @param MinterAPI $api
     * @param LoggerInterface $logger
     * @param bool $print
     * @param array $wallet
     *
     * @return stdClass
     */
    private function getBalance(MinterApi $api, LoggerInterface $logger, bool $print, array $wallet) : stdClass
    {
        $fnPrint = static function (stdClass $balance) use ($logger) {
            $logger->info('Balance');
            foreach ($balance->balance as $coinData) {
                $value = MinterConverter::convertToBase($coinData->value);
                $logger->info("\t{$coinData->coin->symbol}: {$value}");
            }
        };

        try {
            $balance = $api->getBalance($wallet['wallet']);
            if ($print) {
                $fnPrint($balance);
            }
        } catch (Throwable) {
            sleep(2);
            try {
                $balance = $api->getBalance($wallet['wallet']);
                if ($print) {
                    $fnPrint($balance);
                }
            } catch (Throwable) {
                sleep(2);
                $balance = $api->getBalance($wallet['wallet']);
                if ($print) {
                    $fnPrint($balance);
                }
            }
        }

        return $balance;
    }
}
