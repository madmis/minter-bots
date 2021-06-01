<?php

namespace App\Services\Messenger;

use App\Dto\Messenger\OneCoinMessage;
use App\Services\PoolsArbitrator;
use App\Services\PoolsStore;
use Minter\MinterAPI;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class OneCoinArbitrateHandler
 * @package App\Services\Messenger
 */
class OneCoinArbitrateHandler implements MessageHandlerInterface
{
    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * OneCoinArbitrateHandler.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * __invoke.
     *
     * @param OneCoinMessage $message
     *
     * @return void
     * @throws \JsonException
     */
    public function __invoke(OneCoinMessage $message)
    {
        $poolsStore = new PoolsStore();
        $indexedCoins = $poolsStore->coinsIndexedById();
        $pools = $poolsStore->getPools();
        $arbitrator = new PoolsArbitrator($this->logger);
        $txAmount = 7500;
        $readApi = new MinterAPI('https://api.minter.one/v2/');
        $writeApi = new MinterAPI('https://api.minter.one/v2/');
        $walletsFile = '/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json';
        /** @noinspection PhpUnhandledExceptionInspection */
        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));
        $walletIdx = array_rand($wallets);
        $iterations = 2;

        if (isset($indexedCoins[$message->getCoinId()])) {
            $coin = $indexedCoins[$message->getCoinId()];

            if (isset($pools[$coin->getSymbol()])) {
                for ($i = 0; $i < $iterations; $i++) {
                    $arbitrator->arbitrate(
                        $pools[$coin->getSymbol()],
                        $txAmount,
                        $readApi,
                        $writeApi,
                        0,
                        $walletIdx,
                        $wallets
                    );
                }
            }
        }
    }
}
