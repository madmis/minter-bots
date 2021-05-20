<?php

namespace App\Command;

use App\Dto\CoinDto;
use App\Services\PoolsArbitrator;
use App\Services\PoolsStore;
use Minter\MinterAPI;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class NamedPoolsArbitrationCommand
 * @package App\Command
 */
class NamedPoolsArbitrationCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:named-pools:arbitrate';

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
            ->setDescription('Arbitrate in named pools')
            ->addOption(
                'read-node',
                null,
                InputOption::VALUE_REQUIRED,
                'Minter read node url',
                'https://api.minter.one/v2/'
            )
            ->addOption(
                'write-node',
                null,
                InputOption::VALUE_REQUIRED,
                'Minter write node url',
                'https://api.minter.one/v2/'
            )
            ->addOption('req-delay', 'd', InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 200000)
            ->addOption('tx-amounts', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Transaction amounts', [3847])
            ->addOption('iterations', 'i', InputOption::VALUE_REQUIRED, 'Iterations count to repeat requests in the pools', 3)
            ->addOption('pools', 'p', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Pools (Coin symbol)', [])
            ->addOption('custom-coin-pool', null, InputOption::VALUE_NONE, "Set if it's custom coin pool")
            ->addOption(
                'one-bip-in-custom-coin-price',
                null,
                InputOption::VALUE_REQUIRED,
                'If pool with not BIP as goal, set one bip price in this coin to properly calculate fee',
                10000000.00
            )
            ->addOption(
                'wallets-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to JSON wallets file',
                '/var/www/ccbip/resources/wallets/4e4557-5d097c.json'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $readNodeUrl = $input->getOption('read-node');
        $writeNodeUrl = $input->getOption('write-node');
        $txAmounts = $input->getOption('tx-amounts');
        $reqDelay = (int) $input->getOption('req-delay');
        $readApi = new MinterAPI($readNodeUrl);
        $writeApi = new MinterAPI($writeNodeUrl);
        $walletsFile = $input->getOption('wallets-file');
        $iterations = (int) $input->getOption('iterations');
        $pools = $input->getOption('pools');
        $isCustomRoute = (bool) $input->getOption('custom-coin-pool');
        $oneBipInCustomCoinPrice = (float) $input->getOption('one-bip-in-custom-coin-price');

        $arbitrator = new PoolsArbitrator($this->logger);

        $poolsDef = $this->getPools();
        $routes = [];

        foreach ($pools as $pool) {
            if (isset($poolsDef[$pool])) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $routes = array_merge($routes, $poolsDef[$pool]);
            }
        }

        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));

        for ($i = 0; $i < $iterations; $i++) {
            $this->logger->debug("Iteration: {$i}");
            $arbitrator->arbitrate(
                $routes,
                array_rand(array_flip($txAmounts)),
                $readApi,
                $writeApi,
                $reqDelay,
                0,
                $wallets,
                $isCustomRoute,
                $oneBipInCustomCoinPrice,
            );
        }

        return self::SUCCESS;
    }

    /**
     * getPools.
     *
     * @return CoinDto[][][]
     */
    private function getPools() : array
    {
        return (new PoolsStore())->getPools();
    }
}
