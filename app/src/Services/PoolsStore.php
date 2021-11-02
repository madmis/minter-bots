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
        return $this->allCoins();
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
        $btc = new CoinDto(2064, 'BTC');
        $eth = new CoinDto(2065, 'ETH');
        $pint = new CoinDto(24, 'PINT');
        $novocoin = new CoinDto(2123, 'NOVOCOIN');
        $novacoin = new CoinDto(1977, 'NOVACOIN');
        $wolf = new CoinDto(2048, 'WOLF');
        $navalny = new CoinDto(2054, 'NAVALNY');
        $neverminter = new CoinDto(2125, 'NEVRMINTER');
        $lashin = new CoinDto(10, 'LASHIN');
        $mars2043 = new CoinDto(1692, 'MARS2043');
        $elonMusk = new CoinDto(1992, 'ELONMUSK');
        $oracul = new CoinDto(1084, 'ORACUL');
        $only1hub = new CoinDto(2025, 'ONLY1HUB');
        $arcona = new CoinDto(2137, 'ARCONA');
        $bee = new CoinDto(2361, 'BEE');
        $bipBill = new CoinDto(2734, 'BIPBILL');

        return [
            'CUSTOM-USDTE-2' => [
                [$usdte, $musd, $hub, $usdte],
                [$usdte, $hub, $musd, $usdte],
                [$usdte, $usdce, $hub, $usdte],
                [$usdte, $hub, $usdce, $usdte],
                [$usdte, $hub, $bip, $usdte],
                [$usdte, $bip, $hub, $usdte],
                [$usdte, $arcona, $bip, $usdte],
                [$usdte, $bip, $arcona, $usdte],
                [$usdte, $arcona, $musd, $usdte],
                [$usdte, $musd, $arcona, $usdte],
            ],
            'CUSTOM-USDCE-2' => [
                [$usdce, $musd, $hub, $usdce],
                [$usdce, $hub, $musd, $usdce],
                [$usdce, $usdte, $hub, $usdce],
                [$usdce, $hub, $usdte, $usdce],
                [$usdce, $hub, $bip, $usdce],
                [$usdce, $bip, $hub, $usdce],
            ],
            'CUSTOM-MUSD-2' => [
                [$musd, $usdte, $hub, $musd],
                [$musd, $hub, $usdte, $musd],
                [$musd, $usdce, $hub, $musd],
                [$musd, $hub, $usdce, $musd],
                [$musd, $hub, $bip, $musd],
                [$musd, $bip, $hub, $musd],
                [$musd, $bip, $eth, $musd],
                [$musd, $eth, $bip, $musd],
                [$musd, $arcona, $usdte, $musd],
                [$musd, $usdte, $arcona, $musd],
                [$musd, $arcona, $bip, $musd],
                [$musd, $bip, $arcona, $musd],
            ],
            'CUSTOM-USDX-2' => [
                [$usdx, $usdte, $hub, $usdx],
                [$usdx, $hub, $usdte, $usdx],
                [$usdx, $musd, $hub, $usdx],
                [$usdx, $hub, $musd, $usdx],
                [$usdx, $hub, $bip, $usdx],
                [$usdx, $bip, $hub, $usdx],
            ],
            'ARCONA' => [
                [$bip, $musd, $arcona, $bip],
                [$bip, $arcona, $musd, $bip],
                [$bip, $usdte, $arcona, $bip],
                [$bip, $arcona, $usdte, $bip],
                [$bip, $hub, $arcona, $bip],
                [$bip, $arcona, $hub, $bip],
                [$bip, $arcona, $bipBill, $bip],
                [$bip, $bipBill, $arcona, $bip],
            ],
            'BTC' => [
                [$bip, $usdte, $btc, $bip],
                [$bip, $btc, $usdte, $bip],
                [$bip, $musd, $btc, $bip],
                [$bip, $btc, $musd, $bip],
                [$bip, $hub, $musd, $btc, $bip],
                [$bip, $microb, $btc, $bip],
                [$bip, $btc, $microb, $bip],
            ],
            'ETH' => [
                [$bip, $usdte, $eth, $bip],
                [$bip, $eth, $usdte, $bip],
                [$bip, $usdce, $eth, $bip],
                [$bip, $eth, $usdce, $bip],
                [$bip, $musd, $eth, $bip],
                [$bip, $eth, $musd, $bip],
                [$bip, $hub, $eth, $bip],
                [$bip, $eth, $hub, $bip],
                [$bip, $liquidHub, $eth, $bip],
                [$bip, $eth, $liquidHub, $bip],
            ],
            'CUSTOM-HUBABUBA' => [
                [$hubabuba, $bip, $hub, $hubabuba],
                [$hubabuba, $hub, $bip, $hubabuba],
                [$hubabuba, $hub, $usdte, $bip, $hubabuba],
                [$hubabuba, $hub, $usdce, $bip, $hubabuba],
                [$hubabuba, $usdte, $bip, $hubabuba],
                [$hubabuba, $bip, $usdte, $hubabuba],
                [$hubabuba, $usdte, $hub, $hubabuba],
                [$hubabuba, $hub, $usdte, $hubabuba],
                [$hubabuba, $cap, $bip, $hubabuba],
                [$hubabuba, $bip, $cap, $hubabuba],
            ],
            'CUSTOM-ONLY1HUB' => [
                [$only1hub, $liquidHub, $hub, $only1hub],
                [$only1hub, $hub, $liquidHub, $only1hub],
                [$only1hub, $oracul, $liquidHub, $only1hub],
                [$only1hub, $liquidHub, $oracul, $only1hub],
                [$only1hub, $oracul, $hub, $only1hub],
                [$only1hub, $hub, $oracul, $only1hub],
                [$only1hub, $hub, $bip, $liquidHub, $only1hub],
                [$only1hub, $liquidHub, $bip, $hub, $only1hub],
            ],
            'CUSTOM-MONEHUB' => [
                [$moneHub, $bip, $hub, $moneHub],
                [$moneHub, $hub, $bip, $moneHub],
                [$moneHub, $liquidHub, $bip, $moneHub],
                [$moneHub, $bip, $liquidHub, $moneHub],
                [$moneHub, $liquidHub, $hub, $moneHub],
                [$moneHub, $hub, $liquidHub, $moneHub],
                [$moneHub, $bip, $oracul, $moneHub],
                [$moneHub, $oracul, $bip, $moneHub],
            ],
            'CUSTOM-LIQUIDHUB' => [
                [$liquidHub, $bip, $hub, $liquidHub],
                [$liquidHub, $hub, $monsterHub, $liquidHub],
                [$liquidHub, $monsterHub, $bip, $hub, $liquidHub],
                [$liquidHub, $moneHub, $hub, $liquidHub],
                [$liquidHub, $hub, $moneHub, $liquidHub],
                [$liquidHub, $monsterHub, $hub, $bip, $liquidHub],
                [$liquidHub, $hub, $bip, $liquidHub],
                [$liquidHub, $monsterHub, $oracul, $bip, $liquidHub],
                [$liquidHub, $bip, $oracul, $monsterHub, $liquidHub],
                //                [$liquidHub, $bip, $lambo, $hub, $liquidHub],
                //                [$liquidHub, $bip, $usdte, $hub, $liquidHub],
                //                [$liquidHub, $hub, $usdte, $bip, $liquidHub],
                //                [$liquidHub, $bip, $usdce, $hub, $liquidHub],
                //                [$liquidHub, $hub, $usdce, $bip, $liquidHub],
                //                [$liquidHub, $hub, $hubabuba, $bip, $liquidHub],
                //                [$liquidHub, $bip, $hubabuba, $hub, $liquidHub],
            ],
            'CUSTOM-MONSTERHUB' => [
                [$monsterHub, $bip, $hub, $monsterHub],
                [$monsterHub, $hub, $bip, $monsterHub],
                [$monsterHub, $liquidHub, $hub, $monsterHub],
                [$monsterHub, $hub, $liquidHub, $monsterHub],
                [$monsterHub, $liquidHub, $bip, $monsterHub],
                [$monsterHub, $bip, $liquidHub, $monsterHub],
                [$monsterHub, $oracul, $bip, $monsterHub],
                [$monsterHub, $bip, $oracul, $monsterHub],

                //                [$monsterHub, $bip, $lambo, $hub, $monsterHub],
                //                [$monsterHub, $hub, $usdte, $bip, $monsterHub],
                //                [$monsterHub, $hub, $moneHub, $liquidHub, $monsterHub],
                //                [$monsterHub, $bip, $hubChain, $hub, $monsterHub],
                //                [$monsterHub, $hub, $cap, $bip, $monsterHub],
                //                [$monsterHub, $bip, $usdte, $hub, $monsterHub],
                //                [$monsterHub, $hub, $usdte, $bip, $monsterHub],
                //                [$monsterHub, $hub, $usdce, $bip, $monsterHub],
                //                [$monsterHub, $bip, $usdce, $hub, $monsterHub],
                //                [$monsterHub, $hub, $hubabuba, $bip, $monsterHub],
                //                [$monsterHub, $bip, $hubabuba, $hub, $monsterHub],
            ],
            'CUSTOM-MUSD' => [
                [$musd, $bip, $usdx, $musd],
                [$musd, $hub, $usdx, $musd],
                [$musd, $hub, $hubabuba, $bip, $musd],
                [$musd, $hub, $monsterHub, $bip, $musd],
                [$musd, $hub, $lambo, $bip, $musd],
                [$musd, $hub, $hubChain, $bip, $musd],
                [$musd, $hub, $liquidHub, $bip, $musd],
                [$musd, $hub, $moneHub, $bip, $musd],
                [$musd, $hub, $cap, $bip, $musd],
                [$musd, $usdte, $hub, $musd],
                [$musd, $usdx, $bip, $musd],
                [$musd, $usdce, $usdte, $musd],
                [$musd, $usdte, $usdce, $musd],
                [$musd, $usdx, $usdte, $musd],
                [$musd, $usdte, $usdx, $musd],
                [$musd, $usdte, $usdx, $musd],
                [$musd, $usdx, $usdte, $musd],
            ],
            'CUSTOM-USDCE' => [
                [$usdce, $bip, $usdx, $usdce],
                [$usdce, $hub, $usdx, $usdce],
                [$usdce, $hub, $hubabuba, $bip, $usdce],
                [$usdce, $hub, $monsterHub, $bip, $usdce],
                [$usdce, $hub, $lambo, $bip, $usdce],
                [$usdce, $hub, $hubChain, $bip, $usdce],
                [$usdce, $hub, $liquidHub, $bip, $usdce],
                [$usdce, $hub, $moneHub, $bip, $usdce],
                [$usdce, $hub, $cap, $bip, $usdce],
                [$usdce, $usdte, $hub, $usdce],
                [$usdce, $usdx, $bip, $usdce],
                [$usdce, $musd, $usdte, $usdce],
                [$usdce, $usdte, $musd, $usdce],
                [$usdce, $usdx, $musd, $usdce],
                [$usdce, $musd, $usdx, $usdce],
                [$usdce, $usdte, $usdx, $usdce],
                [$usdce, $usdx, $usdte, $usdce],
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
                [$hub, $monsterHub, $oracul, $bip, $hub],
                [$hub, $bip, $oracul, $monsterHub, $hub],
                //                [$hub, $lambo, $bip, $hub],
                //                [$hub, $bip, $lambo, $hub],
                //                [$hub, $lambo, $bip, $liquidHub, $hub],
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
            'BEE' => [
                [$bip, $bee, $musd, $bip],
                [$bip, $musd, $bee, $bip],
                [$bip, $bee, $musd, $usdce, $bip],
                [$bip, $bee, $musd, $usdte, $bip],
                [$bip, $bee, $musd, $hub, $bip],
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
                [$bip, $usdce, $musd, $hub, $bip],
                [$bip, $bee, $musd, $bip],
                [$bip, $musd, $bee, $bip],
            ],
            'USDTE' => [
                [$bip, $usdte, $usdce, $bip],
                [$bip, $usdce, $usdte, $bip],
                [$bip, $usdte, $usdx, $hub, $bip],
                [$bip, $usdte, $usdx, $bip],
                [$bip, $usdce, $usdte, $hub, $bip],
                [$bip, $hub, $usdte, $bip],
                [$bip, $hub, $usdte, $usdce, $bip],
                [$bip, $usdte, $hub, $bip],
            ],
            'USDCE' => [
                [$bip, $usdce, $usdte, $bip],
                [$bip, $usdte, $usdce, $bip],
                [$bip, $usdce, $musd, $bip],
                [$bip, $musd, $usdce, $bip],
            ],
            'FERRARI' => [
                [$bip, $ferrari, $monsterHub, $bip],
                [$bip, $monsterHub, $ferrari, $bip],
                [$bip, $ferrari, $lambo, $bip],
                [$bip, $lambo, $ferrari, $bip],
                //                [$bip, $lambo, $ferra, $bip],
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
                [$bip, $usdx, $hub, $bip],
                [$bip, $hub, $usdx, $bip],
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
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
                [$bip, $hub, $cap, $bip],
                [$bip, $cap, $hub, $bip],
                [$bip, $microb, $hub, $bip],
                [$bip, $hub, $microb, $bip],
                [$bip, $hub, $usdte, $bip],
                [$bip, $usdte, $hub, $bip],
                [$bip, $hub, $musd, $bip],
                [$bip, $musd, $hub, $bip],
                [$bip, $usdce, $musd, $hub, $bip],
                [$bip, $arcona, $hub, $bip],
                [$bip, $hub, $arcona, $bip],
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
            'MONSTERHUB' => [
                [$bip, $monsterHub, $hub, $bip],
                [$bip, $hub, $monsterHub, $bip],
                [$bip, $monsterHub, $moneHub, $bip],
                [$bip, $moneHub, $monsterHub, $bip],
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
                [$bip, $liquidHub, $hubChain, $bip],
                [$bip, $hubChain, $liquidHub, $bip],
                [$bip, $liquidHub, $oracul, $hubChain, $bip],
                [$bip, $hubChain, $oracul, $liquidHub, $bip],
            ],
            'LIQUIDHUB' => [
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $liquidHub, $hub, $bip],
                [$bip, $usdte, $hub, $liquidHub, $bip],
                [$bip, $musd, $hub, $liquidHub, $bip],
                [$bip, $usdce, $hub, $liquidHub, $bip],
                [$bip, $usdx, $hub, $liquidHub, $bip],
                [$bip, $moneHub, $liquidHub, $bip],
                [$bip, $liquidHub, $moneHub, $bip],
                [$bip, $eth, $liquidHub, $bip],
                [$bip, $liquidHub, $eth, $bip],
            ],
            'HUBABUBA' => [
                [$bip, $hub, $hubabuba, $bip],
                [$bip, $hubabuba, $hub, $bip],
                [$bip, $hubabuba, $microb, $bip],
                [$bip, $microb, $hubabuba, $bip],
                [$bip, $usdce, $hub, $hubabuba, $bip],
                [$bip, $usdte, $hub, $hubabuba, $bip],
                [$bip, $musd, $hub, $hubabuba, $bip],
                [$bip, $usdx, $hub, $hubabuba, $bip],
            ],
            'ONLY1HUB' => [
                [$bip, $hub, $only1hub, $liquidHub, $bip],
                [$bip, $hub, $only1hub, $oracul, $bip],
                [$bip, $liquidHub, $only1hub, $hub, $bip],
                [$bip, $liquidHub, $only1hub, $oracul, $bip],
                [$bip, $oracul, $only1hub, $hub, $bip],
                [$bip, $oracul, $only1hub, $liquidHub, $bip],
            ],
        ];
    }

    public function getShortPools() : array
    {
        $bip = new CoinDto(0, 'BIP');
        $hub = new CoinDto(1902, 'HUB');
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
        $btc = new CoinDto(2064, 'BTC');
        $eth = new CoinDto(2065, 'ETH');
        $oracul = new CoinDto(1084, 'ORACUL');

        return [
            'HUB' => [
                [$bip, $hubabuba, $hub, $bip],
                [$bip, $hub, $hubabuba, $bip],
                [$bip, $hub, $musd, $bip],
                [$bip, $musd, $hub, $bip],
                [$bip, $hub, $usdte, $bip],
                [$bip, $hub, $liquidHub, $bip],
                [$bip, $hub, $cap, $bip],
                [$bip, $oracul, $monsterHub, $bip],
            ],
        ];
    }

    /**
     * allCoins.
     *
     * @var array
     */
    private array $allCoins = [];

    /**
     * allCoins.
     *
     * @return CoinDto[]
     */
    public function allCoins() : array
    {
        if (!$this->allCoins) {
            $this->allCoins = include __DIR__ . '/../../resources/coins.php';
        }

        return $this->allCoins;
    }

    /**
     * coinById.
     *
     * @param int $coinId
     *
     * @return CoinDto
     */
    public function coinById(int $coinId) : CoinDto
    {
        return $this->allCoins()[$coinId];
    }
}
