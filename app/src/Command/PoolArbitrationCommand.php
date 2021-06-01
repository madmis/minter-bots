<?php

namespace App\Command;

use App\Dto\CoinDto;
use App\Services\PoolsArbitrator;
use Minter\MinterAPI;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PoolArbitrationCommand
 * @package App\Command
 */
class PoolArbitrationCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:one-pool:arbitrate';

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallets = [
//        0 => [
//            'wallet' => 'Mx3d6927d293a446451f050b330aee443029be1564',
//            'pk' => '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd',
//        ],
//        1 => [
//            'wallet' => 'Mxa7de32768daa3e3d3273b9e251e424be33858cfa',
//            'pk' => '4d09292487ba49d2b53b3d2685d77569341d4e02e4a6fcc3e621556aa37a3677',
//        ],
        0 => [
            'wallet' => 'Mx8ab4f4f3909182e1dd5bebf239a043960e4e4557',
            'pk' => '30fbeff069a78b69afe75e2b43459af4674cf915b3799a57bb739f236d151f88',
        ],
        1 => [
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
            ->setDescription('Arbitrate in one pool')
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
            ->addOption('tx-amount', null, InputOption::VALUE_REQUIRED, 'Transaction amount', 3847)
            ->addOption('wallet-idx', null, InputOption::VALUE_REQUIRED, 'Wallet index:0(Mx75...097c)', 0)
            ->addOption('pools-file', 'f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path to json file with pools', []);
    }


    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $readNodeUrl = $input->getOption('read-node');
        $writeNodeUrl = $input->getOption('write-node');
        $txAmount = (int) $input->getOption('tx-amount');
        $reqDelay = (int) $input->getOption('req-delay');
        $readApi = new MinterAPI($readNodeUrl);
        $writeApi = new MinterAPI($writeNodeUrl);
        $walletIdx = (int) $input->getOption('wallet-idx');

        $routes = [];
        $files = $input->getOption('pools-file');

        foreach ($files as $file) {
            $fileRoutes = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            foreach ($fileRoutes as $oneRoute) {
                $coins = [];

                foreach ($oneRoute as $coin) {
                    $coins[] = new CoinDto($coin['id'], $coin['name']);
                }
                $routes[] = $coins;
            }
        }

        $arbitrator = new PoolsArbitrator($this->logger);

        while (true) {
            $arbitrator->arbitrate($routes, $txAmount, $readApi, $writeApi, $reqDelay, $walletIdx, $this->wallets);
        }

        return self::SUCCESS;
    }
}
