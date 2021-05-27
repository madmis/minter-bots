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
            1979 => new CoinDto(1979, 'LAMBO'),
            1990 => new CoinDto(1990, 'FERRARI'),
            2009 => new CoinDto(2009, 'FERRA'),
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
        $lambo = new CoinDto(1979, 'LAMBO');
        $ferrari = new CoinDto(1990, 'FERRARI');
        $ferra = new CoinDto(2009, 'FERRA');
        $usdte = new CoinDto(1993, 'USDTE');
        $usdce = new CoinDto(1994, 'USDCE');
        $musd = new CoinDto(2024, 'MUSD');
        $gold = new CoinDto(2058, 'GOLD');

        return [
            'CUSTOM-MONEHUB' => [
                [$moneHub, $bip, $hub, $moneHub],
                [$moneHub, $hub, $bip, $moneHub],
                [$moneHub, $liquidHub, $bip, $moneHub],
                [$moneHub, $bip, $liquidHub, $moneHub],
                [$moneHub, $liquidHub, $hub, $moneHub],
                [$moneHub, $hub, $liquidHub, $moneHub],
            ],
            'CUSTOM-HUBABUBA' => [
                [$hubabuba, $bip, $hub, $hubabuba],
                [$hubabuba, $hub, $bip, $hubabuba],
                [$hubabuba, $hub, $usdte, $bip, $hubabuba],
                [$hubabuba, $hub, $usdce, $bip, $hubabuba],
                [$hubabuba, $cap, $hub, $hubabuba],
                [$hubabuba, $hub, $cap, $hubabuba],
                [$hubabuba, $lambo, $hub, $hubabuba],
                [$hubabuba, $hub, $lambo, $hubabuba],
            ],
            'CUSTOM-LIQUIDHUB' => [
                [$liquidHub, $bip, $hub, $liquidHub],
                [$liquidHub, $hub, $monsterHub, $liquidHub],
                [$liquidHub, $monsterHub, $bip, $hub, $liquidHub],
                [$liquidHub, $moneHub, $hub, $liquidHub],
                [$liquidHub, $hub, $moneHub, $liquidHub],
                [$liquidHub, $monsterHub, $hub, $bip, $liquidHub],
                [$liquidHub, $hub, $bip, $liquidHub],
                [$liquidHub, $bip, $lambo, $hub, $liquidHub],
                [$liquidHub, $bip, $usdte, $hub, $liquidHub],
                [$liquidHub, $hub, $usdte, $bip, $liquidHub],
                [$liquidHub, $bip, $usdce, $hub, $liquidHub],
                [$liquidHub, $hub, $usdce, $bip, $liquidHub],
                [$liquidHub, $hub, $hubabuba, $bip, $liquidHub],
                [$liquidHub, $bip, $hubabuba, $hub, $liquidHub],
            ],
            'CUSTOM-MONSTERHUB' => [
                [$monsterHub, $bip, $lambo, $hub, $monsterHub],
                [$monsterHub, $hub, $usdte, $bip, $monsterHub],
                [$monsterHub, $liquidHub, $hub, $monsterHub],
                [$monsterHub, $hub, $moneHub, $liquidHub, $monsterHub],
                [$monsterHub, $bip, $hubChain, $hub, $monsterHub],
                [$monsterHub, $hub, $cap, $bip, $monsterHub],
                [$monsterHub, $bip, $usdte, $hub, $monsterHub],
                [$monsterHub, $hub, $usdte, $bip, $monsterHub],
                [$monsterHub, $hub, $usdce, $bip, $monsterHub],
                [$monsterHub, $bip, $usdce, $hub, $monsterHub],
                [$monsterHub, $hub, $hubabuba, $bip, $monsterHub],
                [$monsterHub, $bip, $hubabuba, $hub, $monsterHub],
            ],
            'CUSTOM-USDTE' => [
                [$usdte, $bip, $usdx, $usdte],
                [$usdte, $hub, $usdx, $usdte],
                [$usdte, $hub, $hubabuba, $bip, $usdte],
                [$usdte, $hub, $monsterHub, $bip, $usdte],
                [$usdte, $hub, $lambo, $bip, $usdte],
                [$usdte, $hub, $hubChain, $bip, $usdte],
                [$usdte, $hub, $liquidHub, $bip, $usdte],
                [$usdte, $hub, $moneHub, $bip, $usdte],
                [$usdte, $hub, $cap, $bip, $usdte],
                [$usdte, $usdce, $hub, $usdte],
                [$usdte, $usdx, $bip, $usdte],
            ],
            'CUSTOM-USDX' => [
                [$usdx, $usdte, $hub, $usdx],
                [$usdx, $usdte, $bip, $usdx],
                [$usdx, $usdte, $bip, $hub, $usdx],
                [$usdx, $bigmac, $coupon, $usdx],
                [$usdx, $bigmac, $bip, $usdx],
                [$usdx, $coupon, $bip, $usdx],
                [$usdx, $quota, $coupon, $usdx],
                [$usdx, $quota, $bip, $usdx],
                [$usdx, $microb, $bip, $usdx],
                [$usdx, $yankee, $bip, $usdx],
                [$usdx, $ftmusd, $bip, $usdx],
                [$usdx, $bip, $bigmac, $coupon, $usdx],
                [$usdx, $bigmac, $bip, $coupon, $usdx],
                [$usdx, $coupon, $bip, $bigmac, $usdx],
                [$usdx, $coupon, $bigmac, $usdx],
                [$usdx, $bip, $yankee, $usdx],
                [$usdx, $bip, $ftmusd, $usdx],
                [$usdx, $bip, $microb, $usdx],
                [$usdx, $bip, $bigmac, $usdx],
                [$usdx, $bip, $coupon, $usdx],
                [$usdx, $coupon, $quota, $usdx],
                [$usdx, $bip, $quota, $usdx],
            ],
            'CUSTOM-BIGMAC' => [
                [$bigmac, $usdx, $coupon, $bigmac],
                [$bigmac, $coupon, $usdx, $bigmac],
                [$bigmac, $usdx, $bip, $coupon, $bigmac],
                [$bigmac, $coupon, $bip, $usdx, $bigmac],
                [$bigmac, $bip, $coupon, $bigmac],
                [$bigmac, $coupon, $bip, $bigmac],
                [$bigmac, $quota, $usdx, $bigmac],
                [$bigmac, $quota, $coupon, $bigmac],
                [$bigmac, $quota, $bip, $bigmac],
                [$bigmac, $coupon, $microb, $usdx, $bigmac],
                [$bigmac, $usdx, $hub, $bip, $bigmac],
            ],
            'CUSTOM-HUB' => [
                [$hub, $bip, $lp59, $hub],
                [$hub, $usdte, $bip, $hub],
                [$hub, $bip, $usdte, $hub],
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
                [$hub, $lambo, $bip, $hub],
                [$hub, $bip, $lambo, $hub],
                [$hub, $rubx, $bip, $lambo, $hub],
                [$hub, $lambo, $bip, $liquidHub, $hub],
            ],
            'CUSTOM-MICROB' => [
                [$microb, $usdx, $bip, $microb],
                [$microb, $hub, $bip, $microb],
                [$microb, $hub, $usdx, $microb],
                [$microb, $quota, $usdx, $microb],
                [$microb, $hubabuba, $hub, $usdx, $microb],
                [$microb, $hub, $hubabuba, $bip, $microb],
                [$microb, $bip, $hubabuba, $hub, $microb],
            ],
            'MUSD' => [
                [$bip, $musd, $usdte, $hub, $bip],
                [$bip, $usdte, $musd, $hub, $bip],
                [$bip, $hub, $usdte, $musd, $bip],
                [$bip, $musd, $hub, $bip],
                [$bip, $hub, $musd, $bip],
                [$bip, $usdte, $musd, $bip],
                [$bip, $musd, $usdte, $bip],
                [$bip, $musd, $usdce, $bip],
                [$bip, $usdce, $musd, $bip],
                [$bip, $musd, $rubx, $bip],
                [$bip, $rubx, $musd, $bip],
                [$bip, $usdce, $musd, $hub, $bip],
            ],
            'USDTE' => [
                [$bip, $usdte, $usdx, $hub, $bip],
                [$bip, $usdte, $usdx, $bip],
                [$bip, $usdte, $usdce, $bip],
                [$bip, $usdce, $usdte, $bip],
                [$bip, $usdce, $usdte, $hub, $bip],
                [$bip, $hub, $usdte, $bip],
                [$bip, $hub, $usdte, $usdce, $bip],
                [$bip, $usdte, $hub, $bip],
                [$bip, $usdte, $gold, $bip],
                [$bip, $usdte, $gold, $hub, $bip],
            ],
            'FERRARI' => [
                [$bip, $ferrari, $monsterHub, $bip],
                [$bip, $monsterHub, $ferrari, $bip],
                [$bip, $ferrari, $lambo, $bip],
                [$bip, $lambo, $ferrari, $bip],
                //                [$bip, $lambo, $ferra, $bip],
            ],
            'LAMBO' => [
                [$bip, $hub, $lambo, $bip],
                [$bip, $lambo, $hub, $bip],
            ],
            'MONEHUB' => [
                [$bip, $hub, $moneHub, $bip],
                [$bip, $moneHub, $hub, $bip],
                [$bip, $liquidHub, $moneHub, $bip],
                [$bip, $moneHub, $liquidHub, $bip],
                [$bip, $monsterHub, $liquidHub, $moneHub, $bip],
                [$bip, $moneHub, $liquidHub, $monsterHub, $bip],
                [$bip, $hub, $moneHub, $liquidHub, $bip],
                [$bip, $liquidHub, $moneHub, $hub, $bip],
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
                [$bip, $hub, $lambo, $bip],
                [$bip, $lambo, $hub, $bip],
                [$bip, $hub, $usdte, $bip],
                [$bip, $usdte, $hub, $bip],
                [$bip, $usdte, $gold, $hub, $bip],
                [$bip, $usdce, $musd, $hub, $bip],
                [$bip, $hub, $gold, $bip],
                [$bip, $hub, $gold, $usdte, $bip],
            ],
            'MONSTERHUB' => [
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
                [$bip, $monsterHub, $liquidHub, $hub, $bip],
                [$bip, $liquidHub, $hub, $monsterHub, $bip],
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $usdte, $hub, $monsterHub, $bip],
                [$bip, $musd, $hub, $monsterHub, $bip],
                [$bip, $usdce, $hub, $monsterHub, $bip],
                [$bip, $usdx, $hub, $monsterHub, $bip],
            ],
            'HUBCHAIN' => [
                [$bip, $hubChain, $hub, $bip],
                [$bip, $hub, $hubChain, $bip],
            ],
            'LIQUIDHUB' => [
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
                [$bip, $usdte, $hub, $liquidHub, $bip],
                [$bip, $musd, $hub, $liquidHub, $bip],
                [$bip, $usdce, $hub, $liquidHub, $bip],
                [$bip, $usdx, $hub, $liquidHub, $bip],
            ],
            'CAP' => [
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
            ],
            'HUBABUBA' => [
                [$bip, $hub, $hubabuba, $bip],
                [$bip, $hubabuba, $hub, $bip],
                [$bip, $rubx, $hubabuba, $bip],
                [$bip, $hubabuba, $rubx, $bip],
                [$bip, $hubabuba, $microb, $bip],
                [$bip, $microb, $hubabuba, $bip],
                [$bip, $hubabuba, $cap, $bip],
                [$bip, $cap, $hubabuba, $bip],
                [$bip, $usdce, $hub, $hubabuba, $bip],
                [$bip, $usdte, $hub, $hubabuba, $bip],
                [$bip, $musd, $hub, $hubabuba, $bip],
                [$bip, $usdx, $hub, $hubabuba, $bip],
            ],
            'RUBX' => [
                [$bip, $hub, $rubx, $bip],
                [$bip, $rubx, $hub, $bip],
                [$bip, $usdce, $hub, $rubx, $bip],
                [$bip, $usdte, $hub, $rubx, $bip],
                [$bip, $musd, $hub, $rubx, $bip],
                [$bip, $usdx, $hub, $rubx, $bip],
            ],
        ];
    }
}
