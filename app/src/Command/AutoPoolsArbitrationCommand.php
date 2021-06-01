<?php

namespace App\Command;

use App\Services\PoolsArbitrator;
use App\Services\PoolsCollector;
use LogicException;
use Minter\MinterAPI;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AutoPoolsArbitrationCommand
 * @package App\Command
 */
class AutoPoolsArbitrationCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:auto-pool:arbitrate';

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallets = [
        0 => [
            'wallet' => 'Mx7586ad025e0f6665c28528f6844ddd00185d097c',
            'pk' => 'da708822753c1c3f054c1e63d0667fcb7b9e2beb59930880905789c6d82e6025',
        ],
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
            ->setDescription('Collect auto pool and arbitrate in Minter pools.')
            // 'https://mnt.funfasy.dev/v2/' - this node has a very low request limit. It's require min 3 sec delay;
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
                'https://gate-api.minter.network/api/v2/'
            )
            ->addOption('req-delay', null, InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 200000)
            ->addOption('tx-amount', null, InputOption::VALUE_REQUIRED, 'Transaction amount', 300)
            ->addOption(
                'wallet-idx',
                null,
                InputOption::VALUE_REQUIRED,
                'Wallet index:0(Mx75...097c)',
                0
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        throw new LogicException("This command doesn't work properly");

        $readNodeUrl = $input->getOption('read-node');
        $writeNodeUrl = $input->getOption('write-node');
        $txAmount = (int) $input->getOption('tx-amount');
        $reqDelay = (int) $input->getOption('req-delay');
        $readApi = new MinterAPI($readNodeUrl);
        $writeApi = new MinterAPI($writeNodeUrl);
        $walletIdx = (int) $input->getOption('wallet-idx');

        $poolsCollector = new PoolsCollector();
        $routes = array_merge(
//            $poolsCollector->collectRoutesToArbitrate(4),
//            $poolsCollector->collectRoutesToArbitrate(5),
            $poolsCollector->collectRoutesToArbitrate(6),
        );
        $arbitrator = new PoolsArbitrator($this->logger);

        while (true) {
            $arbitrator->arbitrate($routes, $txAmount, $readApi, $writeApi, $reqDelay, $walletIdx, $this->wallets);
        }
    }
}
