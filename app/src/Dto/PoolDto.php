<?php

namespace App\Dto;

use JetBrains\PhpStorm\Pure;

/**
 * Class PoolDto
 * @package App\Dto
 */
class PoolDto
{
    /**
     * coin0.
     *
     * @var CoinDto
     */
    private CoinDto $coin0;

    /**
     * coin1.
     *
     * @var CoinDto
     */
    private CoinDto $coin1;

    /**
     * pool.
     *
     * @var CoinDto
     */
    private CoinDto $pool;

    /**
     * amount0.
     *
     * @var string
     */
    private string $amount0;

    /**
     * amount1.
     *
     * @var string
     */
    private string $amount1;

    /**
     * liquidity.
     *
     * @var string
     */
    private string $liquidity;

    /**
     * liquidityBip.
     *
     * @var string
     */
    private string $liquidityBip;

    /**
     * tradeVolumeBip30d.
     *
     * @var string
     */
    private string $tradeVolumeBip30d;

    /**
     * PoolDto.
     *
     * @param CoinDto $coin0
     * @param CoinDto $coin1
     * @param CoinDto $pool
     * @param string $amount0
     * @param string $amount1
     * @param string $liquidity
     * @param string $liquidityBip
     * @param string $tradeVolumeBip30d
     *
     * @return void
     */
    public function __construct(
        CoinDto $coin0,
        CoinDto $coin1,
        CoinDto $pool,
        string $amount0,
        string $amount1,
        string $liquidity,
        string $liquidityBip,
        string $tradeVolumeBip30d
    ) {
        $this->coin0 = $coin0;
        $this->coin1 = $coin1;
        $this->pool = $pool;
        $this->amount0 = $amount0;
        $this->amount1 = $amount1;
        $this->liquidity = $liquidity;
        $this->liquidityBip = $liquidityBip;
        $this->tradeVolumeBip30d = $tradeVolumeBip30d;
    }

    /**
     * fromArray.
     *
     * @param array $pool
     *
     * @return static
     */
    #[Pure] public static function fromArray(array $pool) : self
    {
        return new self(
            new CoinDto($pool['coin0']['id'], $pool['coin0']['symbol']),
            new CoinDto($pool['coin1']['id'], $pool['coin1']['symbol']),
            new CoinDto($pool['token']['id'], $pool['token']['symbol']),
            $pool['amount0'],
            $pool['amount1'],
            $pool['liquidity'],
            $pool['liquidity_bip'],
            $pool['trade_volume_bip_30d'],
        );
    }

    /**
     * equals.
     *
     * @param PoolDto $poolDto
     *
     * @return bool
     */
    public function equals(PoolDto $poolDto) : bool
    {
        return $poolDto->getPool()->equals($this->getPool());
    }

    /**
     * @return CoinDto
     */
    public function getPool() : CoinDto
    {
        return $this->pool;
    }

    /**
     * @return CoinDto
     */
    public function getCoin0() : CoinDto
    {
        return $this->coin0;
    }

    /**
     * @return CoinDto
     */
    public function getCoin1() : CoinDto
    {
        return $this->coin1;
    }

    /**
     * @return string
     */
    public function getAmount0() : string
    {
        return $this->amount0;
    }

    /**
     * @return string
     */
    public function getAmount1() : string
    {
        return $this->amount1;
    }

    /**
     * @return string
     */
    public function getLiquidity() : string
    {
        return $this->liquidity;
    }

    /**
     * @return string
     */
    public function getLiquidityBip() : string
    {
        return $this->liquidityBip;
    }

    /**
     * @return string
     */
    public function getTradeVolumeBip30d() : string
    {
        return $this->tradeVolumeBip30d;
    }

    /**
     * hasCoin.
     *
     * @param CoinDto $coin
     *
     * @return bool
     */
    #[Pure] public function hasCoin(CoinDto $coin) : bool
    {
        return $this->getCoin0()->equals($coin) || $this->getCoin1()->equals($coin);
    }
}
