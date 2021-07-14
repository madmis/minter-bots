<?php

namespace App\Command\BalanceBot;

use App\Dto\CoinDto;
use App\Services\PoolsStore;
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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class ArbitrateStableFromBalanceCommand
 * @package App\Command\BalanceBot
 */
class ArbitrateStableFromBalanceCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:stable:arbitrate';


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
            ->setDescription('Collect auto pool and arbitrate in Minter pools.')
            ->addOption('coins-ids', null, InputOption::VALUE_REQUIRED, 'Coins ids to trade: 1,2,3', '')
            ->addOption('trade-amount', null, InputOption::VALUE_REQUIRED, 'Trade amount', 100)
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
        $api = new MinterAPI('https://api.minter.one/v2/');
        $coins = array_map(static fn($id) => (int) trim($id), explode(',', $input->getOption('coins-ids')));

        if (!$coins) {
            $this->logger->error('Coins not defined');

            return self::FAILURE;
        }
        $walletsFile = $input->getOption('wallets-file');
        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));
        $walletIdx = array_rand($wallets);

        $coins = array_combine($coins, $coins);
        $tradeAmount = (float) $input->getOption('trade-amount');
        $minMargin = (float) $input->getOption('min-margin');
        $allCoins = (new PoolsStore())->allCoins();
        $wallet = $wallets[$walletIdx];

        $balance = $this->getBalance($api, $this->logger, true, $wallet);
        $reqDelay = 100000;
        $minGasPrice = 2;

        while (true) {
            foreach ($balance->balance as $coinData) {
                if (isset($coins[$coinData->coin->id])) {
                    $amount = (float) MinterConverter::convertToBase($coinData->value);
                    if ($amount >= $tradeAmount) {
                        foreach ($coins as $coinId) {
                            if ((int) $coinId !== (int) $coinData->coin->id) {
                                $route = [$allCoins[$coinData->coin->id], $allCoins[$coinId]];

                                try {
                                    $r = implode('=>', array_map(
                                        static fn(CoinDto $coin) => $coin->getSymbol(),
                                        $route,
                                    ));

                                    try {
                                        $this->logger->debug("R: {$r}");

//                                        $minAmountToBuy = $tradeAmount + ($minMargin * $minGasPrice);
                                        $minAmountToBuy = $tradeAmount + $minMargin;
                                        $this->logger->debug("minAmountToBuy: {$minAmountToBuy}");
                                        $data = new MinterSellSwapPoolTx(
                                            array_map(static fn(CoinDto $coin) => $coin->getId(), $route),
                                            $tradeAmount,
                                            $minAmountToBuy
                                        );
                                        $nonce = $api->getNonce($wallet['wallet']);
                                        $tx = (new MinterTx($nonce, $data))->setGasPrice($minGasPrice);
                                        $signedTx = $tx->sign($wallet['pk']);
                                        $response = $api->send($signedTx);
                                        $this->logger->info("R: {$r}");
                                        $this->logger->info("\t Amount: {$tradeAmount}. Min Buy Amount {$minAmountToBuy}. Gas Price: {$minGasPrice}");
                                        $this->logger->info("\t", ['response' => (array) $response]);
                                        sleep(5);
                                        $balance = $this->getBalance($api, $this->logger, true, $wallet);

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
                                            $balance = $this->getBalance($api, $this->logger, true, $wallet);
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
        }

        return self::SUCCESS;
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
