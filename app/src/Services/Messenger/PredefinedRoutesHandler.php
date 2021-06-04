<?php

namespace App\Services\Messenger;


use App\Dto\Messenger\PredefinedRoutesMessage;
use App\Services\PoolsArbitrator;
use App\Services\PoolsStore;
use Minter\MinterAPI;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PredefinedRoutesHandler implements MessageHandlerInterface
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
     * @param PredefinedRoutesMessage $message
     *
     * @return void
     * @throws \JsonException
     */
    public function __invoke(PredefinedRoutesMessage $message)
    {
        $indexedCoins = (new PoolsStore())->coinsIndexedById();
        $arbitrator = new PoolsArbitrator($this->logger);
        $txAmount = 7000;
        $readApi = new MinterAPI('https://api.minter.one/v2/');
        $writeApi = new MinterAPI('https://api.minter.one/v2/');
        $walletsFile = '/var/www/ccbip/resources/wallets/2-be1564-858cfa.json';
        /** @noinspection PhpUnhandledExceptionInspection */
        $wallets = array_values(json_decode(file_get_contents($walletsFile), true, 512, JSON_THROW_ON_ERROR));
        $walletIdx = array_rand($wallets);
        $iterations = 1;

        $routes = [];

        foreach ($message->getRoutes() as $route) {
            $rt = [];
            foreach ($route as $coinId) {
                $rt[] = $indexedCoins[$coinId];
            }

            $routes[] = $rt;
        }

        // Возможно чувак делает так
        // Он смотрит транзакцию из мемпула и шлет в мемпул транзакцию по бипам
        // с этим же роутом и суммой, которую сейчас можно получить не получив отлуп
        // (есть метод проверки минимальной суммы которую можно получить).
        // таким образом транзакция сразу же попадает в этот же блок и потом берет ликвидность из текущей транзы


        for ($i = 0; $i < $iterations; $i++) {
            $arbitrator->arbitrate(
                $routes,
                $txAmount,
                $readApi,
                $writeApi,
                0,
                $walletIdx,
                $wallets
            );
            sleep(1);
        }
    }
}
