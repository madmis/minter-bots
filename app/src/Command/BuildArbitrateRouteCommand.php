<?php /** @noinspection SlowArrayOperationsInLoopInspection */

namespace App\Command;

use App\Dto\CoinDto;
use App\Services\PoolsCollector;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BuildArbitrateRouteCommand
 * @package App\Command
 */
class BuildArbitrateRouteCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:arbitrate:build:routes';

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
        $this->setDescription('Build arbitrate routes.')
            ->addOption('route-levels', 'l', InputOption::VALUE_REQUIRED, 'Route levels', 4);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $levels = (int) $input->getOption('route-levels');
        $collector = new PoolsCollector();
        $routes = $collector->collectRoutesToArbitrate($levels);

        $fr = [];

        foreach ($routes as $route) {
            $fr[] = implode('=>', array_map(
                static fn(CoinDto $coin) => $coin->getSymbol(),
                $route,
            ));
        }

        $this->logger->info('', ['routes' => $fr]);

        return self::SUCCESS;
    }
}
