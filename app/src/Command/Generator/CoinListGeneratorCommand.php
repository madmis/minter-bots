<?php


namespace App\Command\Generator;

use App\Services\PoolsStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoinListGeneratorCommand
 * @package App\Command\Generator
 */
class CoinListGeneratorCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'generator:coins';

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
            ->setDescription('Consume messages published by local miner node.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $json = file_get_contents('https://explorer-api.minter.network/api/v2/coins');
        $data = json_decode($json, true);

        $mainTpl = <<<'TPL'
<?php

use App\Dto\CoinDto;

return [
    %s
];
TPL;
        $coinTpl = <<<TPL
    %d => new CoinDto(%d, '%s'),    
    '%s' => new CoinDto(%d, '%s'),    
TPL;

        $coins = [];

        foreach ($data['data'] as $coin) {
            $coins[] = sprintf(
                $coinTpl,
                $coin['id'],
                $coin['id'],
                $coin['symbol'],
                $coin['symbol'],
                $coin['id'],
                $coin['symbol'],
            );
        }

        $fileData = sprintf($mainTpl, implode(PHP_EOL, $coins));
        $filePath = __DIR__ . '/../../../resources/coins.php';
        file_put_contents($filePath, $fileData);

        return self::SUCCESS;
    }
}
