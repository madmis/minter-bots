<?php /** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection SlowArrayOperationsInLoopInspection */

namespace App\Services;

use App\Dto\CoinDto;
use App\Dto\PoolDto;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\Pure;

class PoolsCollector
{
    /**
     * bip.
     *
     * @var CoinDto
     */
    private CoinDto $bip;

    /**
     * PoolsCollector.
     *
     * @return void
     */
    #[Pure] public function __construct()
    {
        $this->bip = new CoinDto(0, 'BIP');
    }

    /**
     * collectRoutesToArbitrate.
     *
     * @param int $routesLevel
     *
     * @return CoinDto[][]
     */
    public function collectRoutesToArbitrate(int $routesLevel) : array
    {
        $pools = $this->getPools();
        $routes = [];

        foreach ($pools as $pool) {
            $level1 = PoolDto::fromArray($pool);

            if ($level1->getCoin0()->equals($this->bip)) {
                /** @noinspection PhpSingleStatementWithBracesInspection */
                $routes = array_merge($routes, $this->buildRoutes($pools, $level1, $routesLevel));
            }
        }

        $routesWithCoins = [];

        foreach ($routes as $route) {
            $routesWithCoins[] = $this->buildFinalRoute($route);
        }

        return $routesWithCoins;
    }


    /**
     * addRouteLevel.
     *
     * @param array $pools
     * @param PoolDto[] $route
     * @param bool $finalizeRoute
     *
     * @return array
     */
    private function addRouteLevel(array &$pools, array $route, bool $finalizeRoute) : array
    {
        $lastPool = end($route);

        foreach ($pools as $key => $pool) {
            $poolDto = PoolDto::fromArray($pool);

            if ($poolDto->equals($lastPool)) {
                continue;
            }

            if ($finalizeRoute) {
                if ($poolDto->hasCoin($this->bip) && $poolDto->hasCoin($lastPool->getCoin1())) {
                    $route[] = $poolDto;

                    return $route;
                }
            } else {
                if (!$poolDto->hasCoin($this->bip) && $poolDto->hasCoin($lastPool->getCoin1())) {
                    $route[] = $poolDto;
                    unset($pools[$key]);

                    return $route;
                }
            }
        }

        return $route;
    }

    /**
     * buildRoute.
     *
     * @param array $pools
     * @param PoolDto $basePool
     * @param int $levels
     *
     * @return array
     */
    private function buildRoutes(array $pools, PoolDto $basePool, int $levels = 4) : array
    {
        $baseRoute = [$basePool];
        $routes = [];

        do {
            $route = null;
            for ($i = 3; $i < $levels; $i++) {
                $route = $this->addRouteLevel($pools, $route ?? $baseRoute, false);
            }
            $route = $this->addRouteLevel($pools, $route ?? $baseRoute, true);

            if (count($route) + 1 === $levels) {
                $routes[] = $route;
            }
        } while (count($route) + 1 === $levels);

        return $routes;
    }

    /**
     * buildFinalRoute.
     *
     * @param PoolDto[] $routeWithPools
     *
     * @return CoinDto[]
     */
    #[Pure] private function buildFinalRoute(array $routeWithPools) : array
    {
        $route = [];

        foreach ($routeWithPools as $key => $pool) {
            if ($key === 0) {
                $route[] = $pool->getCoin0();
                $lastRouteCoin = $pool->getCoin1();
                $route[] = $lastRouteCoin;

                continue;
            }

            if ($key === count($routeWithPools) - 1) {
                // last route element. Search bip
                $route[] = $pool->getCoin0()->equals($this->bip) ? $pool->getCoin0() : $pool->getCoin1();
                continue;
            }

            if (isset($lastRouteCoin)) {
                $route[] = $pool->getCoin0()->equals($lastRouteCoin)
                    ? $pool->getCoin1()
                    : $pool->getCoin0();
            }
        }

        return $route;
    }

    /**
     * getPools.
     *
     * @return array
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function getPools() : array
    {
        $client = new Client();
        $items = [];

        do {
            $poolsUrl = $data['links']['next'] ?? 'https://explorer-api.minter.network/api/v2/pools';
            $response = $client->get($poolsUrl);
            $content = $response->getBody()->getContents();
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $items = array_merge($items, $data['data']);
        } while (!empty($data['links']['next']));

        return $items;
    }
}
