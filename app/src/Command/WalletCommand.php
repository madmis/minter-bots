<?php

namespace App\Command;

use Minter\SDK\MinterWallet;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WalletCommand
 * @package App\Command
 */
class WalletCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:wallet';

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

    protected function configure()
    {
        $this
            ->setDescription('Test command.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $wallet = MinterWallet::createFromMnemonic(MinterWallet::generateMnemonic());
        dump($wallet->getAddress());
        dump($wallet->getPrivateKey());
        dump($wallet->getMnemonic());

        return self::SUCCESS;
    }
}
