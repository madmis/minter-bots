<?php

namespace App\Services;

use App\Dto\CoinDto;
use JetBrains\PhpStorm\Pure;

/**
 * Class PoolsStore
 * @package App\Services
 */
class PoolsStore
{
    /**
     * coinsIndexedById.
     *
     * @return CoinDto[]
     */
    public function coinsIndexedById() : array
    {
        return [
            0 => new CoinDto(0, 'BIP'),
            1902 => new CoinDto(1902, 'HUB'),
            1784 => new CoinDto(1784, 'RUBX'),
            1895 => new CoinDto(1895, 'MONSTERHUB'),
            1893 => new CoinDto(1893, 'LIQUIDHUB'),
            1900 => new CoinDto(1900, 'HUBCHAIN'),
            1934 => new CoinDto(1934, 'CAP'),
            1942 => new CoinDto(1942, 'HUBABUBA'),
            907 => new CoinDto(907, 'BIGMAC'),
            1678 => new CoinDto(1678, 'USDX'),
            1086 => new CoinDto(1086, 'QUOTA'),
            1043 => new CoinDto(1043, 'COUPON'),
            1087 => new CoinDto(1087, 'MICROB'),
            1048 => new CoinDto(1048, 'FTMUSD'),
            21 => new CoinDto(21, 'FREEDOM'),
            1074 => new CoinDto(1074, 'YANKEE'),
            1901 => new CoinDto(1901, 'MONEHUB'),
            1905 => new CoinDto(1905, 'LP-59'),
        ];
    }

    /**
     * getPools.
     *
     * @return CoinDto[][][]
     */
    public function getPools() : array
    {
        $bip = new CoinDto(0, 'BIP');
        $hub = new CoinDto(1902, 'HUB');
        $rubx = new CoinDto(1784, 'RUBX');
        $monsterHub = new CoinDto(1895, 'MONSTERHUB');
        $liquidHub = new CoinDto(1893, 'LIQUIDHUB');
        $hubChain = new CoinDto(1900, 'HUBCHAIN');
        $cap = new CoinDto(1934, 'CAP');
        $hubabuba = new CoinDto(1942, 'HUBABUBA');
        $bigmac = new CoinDto(907, 'BIGMAC');
        $usdx = new CoinDto(1678, 'USDX');
        $quota = new CoinDto(1086, 'QUOTA');
        $coupon = new CoinDto(1043, 'COUPON');
        $microb = new CoinDto(1087, 'MICROB');
        $ftmusd = new CoinDto(1048, 'FTMUSD');
        $freedom = new CoinDto(21, 'FREEDOM');
        $yankee = new CoinDto(1074, 'YANKEE');
        $moneHub = new CoinDto(1901, 'MONEHUB');
        $lp59 = new CoinDto(1905, 'LP-59');

        return [
            'CUSTOM-HUB' => [
                [$hub, $bip, $lp59, $hub],
                [$hub, $lp59, $bip, $hub],
                [$hub, $hubChain, $bip, $hub],
                [$hub, $bip, $hubChain, $hub],
                [$hub, $bip, $rubx, $hub],
                [$hub, $rubx, $bip, $hub],
                [$hub, $liquidHub, $monsterHub, $hub],
                [$hub, $monsterHub, $liquidHub, $hub],
                [$hub, $liquidHub, $moneHub, $hub],
                [$hub, $moneHub, $liquidHub, $hub],
                [$hub, $bip, $moneHub, $hub],
                [$hub, $moneHub, $bip, $hub],
                [$hub, $hubabuba, $bip, $hub],
                [$hub, $bip, $hubabuba, $hub],
                [$hub, $cap, $bip, $hub],
                [$hub, $bip, $cap, $hub],
                [$hub, $usdx, $bip, $hub],
                [$hub, $bip, $usdx, $hub],
                [$hub, $moneHub, $liquidHub, $bip, $hub],
                [$hub, $microb, $bip, $hub],
                [$hub, $bip, $microb, $hub],
            ],
            'MONEHUB' => [
                [$bip, $hub, $moneHub, $bip],
                [$bip, $moneHub, $hub, $bip],
                [$bip, $liquidHub, $moneHub, $bip],
                [$bip, $moneHub, $liquidHub, $bip],
            ],
            'YANKEE' => [
                [$bip, $usdx, $yankee, $bip],
                [$bip, $yankee, $usdx, $bip],
            ],
            'FTMUSD' => [
                [$bip, $ftmusd, $usdx, $bip],
                [$bip, $usdx, $ftmusd, $bip],
                [$bip, $freedom, $ftmusd, $bip],
                [$bip, $ftmusd, $freedom, $bip],
            ],
            'FREEDOM' => [
                [$bip, $freedom, $ftmusd, $bip],
                [$bip, $ftmusd, $freedom, $bip],
            ],
            'MICROB' => [
                [$bip, $microb, $usdx, $bip],
                [$bip, $usdx, $microb, $bip],
                [$bip, $microb, $hub, $bip],
                [$bip, $hub, $microb, $bip],
            ],
            'COUPON' => [
                [$bip, $bigmac, $coupon, $bip],
                [$bip, $coupon, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
            ],
            'QUOTA' => [
                [$bip, $bigmac, $quota, $bip],
                [$bip, $quota, $bigmac, $bip],
                [$bip, $usdx, $coupon, $quota, $bip],
            ],
            'USDX' => [
                [$bip, $bigmac, $usdx, $bip],
                [$bip, $usdx, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
                [$bip, $microb, $usdx, $bip],
                [$bip, $usdx, $microb, $bip],
                [$bip, $usdx, $coupon, $quota, $bip],
                [$bip, $usdx, $hub, $hubabuba, $bip],
                [$bip, $usdx, $yankee, $bip],
                [$bip, $yankee, $usdx, $bip],
                [$bip, $usdx, $bigmac, $quota, $bip],
                [$bip, $usdx, $quota, $bigmac, $bip],
                [$bip, $usdx, $quota, $bip],
            ],
            'BIGMAC' => [
                [$bip, $bigmac, $coupon, $bip],
                [$bip, $coupon, $bigmac, $bip],
                [$bip, $bigmac, $quota, $bip],
                [$bip, $quota, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $bip],
                [$bip, $usdx, $bigmac, $bip],
                [$bip, $bigmac, $usdx, $coupon, $bip],
                [$bip, $bigmac, $coupon, $usdx, $bip],
                [$bip, $usdx, $bigmac, $coupon, $bip],
                [$bip, $usdx, $coupon, $bigmac, $bip],
            ],
            'HUB' => [
                [$bip, $hub, $rubx, $bip],
                [$bip, $rubx, $hub, $bip],
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
                [$bip, $microb, $hub, $bip],
                [$bip, $hub, $microb, $bip],
            ],
            'MONSTERHUB' => [
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
            ],
            'HUBCHAIN' => [
                [$bip, $hubChain, $hub, $bip],
                [$bip, $hub, $hubChain, $bip],
            ],
            'LIQUIDHUB' => [
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
            ],
            'CAP' => [
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
            ],
            'HUBABUBA' => [
                [$bip, $hub, $hubabuba, $bip],
                [$bip, $hubabuba, $hub, $bip],
            ],
            'RUBX' => [
                [$bip, $hub, $rubx, $bip],
                [$bip, $rubx, $hub, $bip],
            ],
        ];
    }
}
