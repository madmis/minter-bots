<?php

namespace App\Command\TON;

use App\Services\TON\TonSwapClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand.
 * @package App\Command\TON
 */
class TestCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'ton:test';

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
            ->setDescription('TON test command');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $cl = new TonSwapClient();
        $manifest = $cl->getManifest();
        $tokens = [];

        foreach ($manifest['tokens'] as $token) {
            $tokens[$token['symbol']] = $token;
        }

        $routes = [
            'WTON=>USDT=>USDC=>WTON' => [
                'baseToken' => 'WTON',
                'pools' => [
                    '0:388bc635625a3c6f424e493223cd38f4ed756a16ab3f477e2288e4b2dec500af',
                    '0:046a69e68cd9c2db0611c0452dfbe98f20e7e843af09f6a20ad55bc839f50ab4',
                    '0:7dec631cd2c01472838d577c7d912916abc3b1503f75e38175b0aed1e0cfefb1',
                ],
            ],
            'WTON=>USDC=>USDT=>=>WTON' => [
                'baseToken' => 'WTON',
                'pools' => [
                    '0:7dec631cd2c01472838d577c7d912916abc3b1503f75e38175b0aed1e0cfefb1',
                    '0:046a69e68cd9c2db0611c0452dfbe98f20e7e843af09f6a20ad55bc839f50ab4',
                    '0:388bc635625a3c6f424e493223cd38f4ed756a16ab3f477e2288e4b2dec500af',
                ],
            ],
            'WTON=>BRIDGE=>WBTC=>WTON' => [
                'baseToken' => 'WTON',
                'pools' => [
                    '0:22e137155647e2ce7d9cb04d538a4be05ea832c3f34290e776e38d2acf5af54f',
                    '0:0d0c10b73ccfc1a54df2e580028574de031365801ff4d037ab005c7eb7f46905',
                    '0:4a843d1dbf8409790d21ef01283c6051ebe96cc4e7184293d094ff07d9f6a574',
                ],
            ],
            'WTON=>WBTC=>BRIDGE=>WTON' => [
                'baseToken' => 'WTON',
                'pools' => [
                    '0:4a843d1dbf8409790d21ef01283c6051ebe96cc4e7184293d094ff07d9f6a574',
                    '0:0d0c10b73ccfc1a54df2e580028574de031365801ff4d037ab005c7eb7f46905',
                    '0:22e137155647e2ce7d9cb04d538a4be05ea832c3f34290e776e38d2acf5af54f',
                ],
            ],
        ];

        while(true) {
            foreach ($routes as $routeKey => $routeConfig) {
                $startAmount = 80;
                $gotAmount = $startAmount;
                $finalMinAmount = 82;

                $this->logger->debug("Route: $routeKey (Start amount: $startAmount)");
                $baseToken = $routeConfig['baseToken'];

                foreach ($routeConfig['pools'] as $address) {
                    $this->logger->debug("\tBase Token: {$baseToken}");
                    try {
                        $poolsInfo = $cl->getPoolInfo($address);
                    } catch (RequestException $e) {
                        sleep(60);
                        break 2;
                    }
                    $this->logger->debug("\tPools tokens: {$poolsInfo['meta']['base']} (base) | {$poolsInfo['meta']['counter']} (counter)");

                    if ($poolsInfo['meta']['base'] === $baseToken) {
                        $leftToken = $poolsInfo['meta']['base'];
                        $rightToken = $poolsInfo['meta']['counter'];
                        $leftDec = $tokens[$leftToken]['decimals'];
                        $rightDec = $tokens[$rightToken]['decimals'];
                        $left = $poolsInfo['leftLocked'] / str_pad('1', ++$leftDec, '0', STR_PAD_RIGHT);
                        $right = $poolsInfo['rightLocked'] / str_pad('1', ++$rightDec, '0', STR_PAD_RIGHT);

                        $price = $right/$left;
                    } else {
                        $leftToken = $poolsInfo['meta']['counter'];
                        $rightToken = $poolsInfo['meta']['base'];
                        $leftDec = $tokens[$leftToken]['decimals'];
                        $rightDec = $tokens[$rightToken]['decimals'];
                        $left = $poolsInfo['rightLocked'] / str_pad('1', ++$leftDec, '0', STR_PAD_RIGHT);
                        $right = $poolsInfo['leftLocked'] / str_pad('1', ++$rightDec, '0', STR_PAD_RIGHT);

                        $price = $right/$left;
                    }

                    $gotAmount *= $price;
                    $this->logger->debug("\t\tLeft token: {$leftToken}. Right token: {$rightToken}");
                    $this->logger->debug(sprintf("\t\tPrice: %01.12f", $price));
                    $this->logger->debug(sprintf("\t\tGot amount: %01.12f", $gotAmount));

                    $baseToken = $rightToken;
                }

                if ($gotAmount >= $finalMinAmount) {
                    $this->logger->info("Route: $routeKey (Start amount: $startAmount)");
                    $this->logger->info("\tStart amount: $startAmount. Final amount: $gotAmount");
                }
            }

            sleep(30);
        }


//        dump($cl->getCurrencies());
//        dump($cl->getPairs());
//        dump($cl->getPoolInfo('0:22e137155647e2ce7d9cb04d538a4be05ea832c3f34290e776e38d2acf5af54f'));

        return self::SUCCESS;
    }
}
