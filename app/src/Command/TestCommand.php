<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\Command;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterBuyCoinTx;
use Minter\SDK\MinterCoins\MinterCreateCoinTx;
use Minter\SDK\MinterCoins\MinterSellAllCoinTx;
use Minter\SDK\MinterCoins\MinterSellCoinTx;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterConverter;
use Minter\SDK\MinterTx;
use Minter\SDK\MinterWallet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Command
 */
class TestCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:test';

    protected function configure()
    {
        $this
            ->setDescription('Test command.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wallet = MinterWallet::createFromMnemonic('ring local dilemma bless injury surprise twist proof jeans language donate tray');
        dump($wallet->getPrivateKey());

        return 0;
        //Боевая сеть: https://api.minter.stakeholder.space/
        //Тестовая сеть: https://api.testnet.minter.stakeholder.space/

//        $nodeUrl = 'https://node-api.taconet.minter.network/v2/';
//        $nodeUrl = 'https://mnt.funfasy.dev/v2/';
        $nodeUrl = 'https://api.minter.one/v2/';
        $api = new MinterAPI($nodeUrl);



        //        array(5) {
        //        'seed' =>
        //  string(128) "bd1eb0e9937a89f25452b049ecfaa884f32dd2fb56b63c969328bda6b2eb129ba1a534c63fc65cb234256a434b809f5e6436c534a3316f436ee8eeb8809f0f26"
        //  'address' =>
        //  string(42) "Mx3d6927d293a446451f050b330aee443029be1564"
        //  'mnemonic' =>
        //  string(79) "arrange include say lounge knock century unique swim warfare slush raise planet"
        //  'public_key' =>
        //  string(130) "Mp071d479665dd3ece05eb68e9552a23f3bb76096d411a36806eb79d7777d2dded2b96ec9744200c90a16385ecb70f4a534168dbbaebb61f43504b0a2b966921fc"
        //  'private_key' =>
        //  string(64) "76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd"
        //}
        $address = 'Mx3d6927d293a446451f050b330aee443029be1564';
        $privateKey = '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd';
        var_dump(MinterConverter::convertValue('6000000000000000000000', 'bip'));

//        $nodeUrl = 'https://minter-node-1.testnet.minter.network:8841'; // example of a node url
        $nodeUrl = 'https://api.minter.stakeholder.space/'; // example of a node url
//        $nodeUrl = 'https://api.minter.stakeholder.space/';
        $api = new MinterAPI($nodeUrl);
//        $wallet = MinterWallet::create();
//        var_dump($wallet);

        $items = [
            "achieve",
            "addict",
            "album",
            "allow",
            "angry",
            "april",
            "arrow",
            "auction",
            "bachelor",
            "base",
            "beyond",
            "blind",
            "boil",
            "bright",
            "buffalo",
            "busy",
            "capable",
            "case",
            "chair",
            "cherry",
            "city",
            "clever",
            "combine",
            "conduct",
            "craft",
            "crane",
            "cruel",
            "cycle",
            "deer",
            "desk",
            "devote",
            "dish",
            "donor",
            "dream",
            "dust",
            "elephant",
            "emotion",
            "equal",
            "erosion",
            "exit",
            "express",
            "father",
            "festival",
            "fire",
            "flock",
            "force",
            "frost",
            "gas",
            "gentle",
            "give",
            "goose",
            "grid",
            "hair",
            "high",
            "hockey",
            "hunt",
            "impact",
            "infant",
            "inside",
            "island",
            "junk",
            "kite",
            "labor",
            "legal",
            "link",
            "lobster",
            "lunch",
            "manage",
            "maze",
            "mesh",
            "minimum",
            "mosquito",
            "move",
            "need",
            "never",
            "notable",
            "old",
            "olympic",
            "order",
            "ozone",
            "patch",
            "pelican",
            "piece",
            "please",
            "present",
            "project",
            "public",
            "quote",
            "radio",
            "raw",
            "remove",
            "require",
            "ribbon",
            "roof",
            "route",
            "sample",
            "science",
            "segment",
            "session",
            "shrug",
            "similar",
            "skill",
            "sniff",
            "solution",
            "space",
            "stable",
            "stamp",
            "struggle",
            "sunny",
            "swing",
            "tail",
            "taxi",
            "tide",
            "timber",
            "tone",
            "train",
            "truck",
            "turn",
            "umbrella",
            "until",
            "usual",
            "venue",
            "vocal",
            "wait",
            "weekend",
            "where",
            "worth",
            "yellow",
        ];

        foreach ($items as $key => $item) {
            $output->writeln("{$key}: {$item}");
            $mnemonic = "canvas people cram lobster journey cream vocal damp annual blade bind {$item}";
            $seed = MinterWallet::mnemonicToSeed($mnemonic);
            $privateKey = MinterWallet::seedToPrivateKey($seed);
            $publicKey = MinterWallet::privateToPublic($privateKey);
            $address = MinterWallet::getAddressFromPublicKey($publicKey);
            $balance = $api->getBalance($address);
            if ($balance->result->transaction_count) {
                $output->writeln('************************************');
                $output->writeln($mnemonic);
                $output->writeln($address);
                var_dump($balance);
                $output->writeln('************************************');
            }

            sleep(5);
        }


//        var_dump($api->getValidators());
        exit();
//        var_dump($api->getStatus());
//        var_dump($api->estimateCoinBuy('MNT', 1, 'MNT'));

        $coinSymbol = '9OOOOOOOO5';
        $mntSymbol = 'MNT';
        $output->writeln("CREATE {$coinSymbol}");
        $txCreate = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterCreateCoinTx::TYPE,
            'data' => [
                'name' => 'Test Coin',
                'symbol' => $coinSymbol,
                'initialAmount' => '1000',
                'initialReserve' => '1000',
                'crr' => 40,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);
//        try {
//            $response = $api->send($txCreate->sign($privateKey));
//            print_r($response);
//        } catch(RequestException $exception) {
//            var_dump($exception->getResponse()->getBody()->getContents());
//        }

        // BUY COIN
        foreach ([100, 50, 100] as $qty) {
            $output->writeln("BUY {$qty}");
            sleep(10);
            $txBuy = new MinterTx([
                'nonce' => $api->getNonce($address),
                'chainId' => MinterTx::TESTNET_CHAIN_ID,
                'gasPrice' => 1,
                'gasCoin' => $mntSymbol,
                'type' => MinterBuyCoinTx::TYPE,
                'data' => [
                    'coinToBuy' => $coinSymbol,
                    'valueToBuy' => "{$qty}",
                    'coinToSell' => $mntSymbol,
                    'maximumValueToSell' => 2000,
                ],
                'payload' => '',
                'serviceData' => '',
                'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE,
            ]);

//            try {
//                $response = $api->send($txBuy->sign($privateKey));
//            $response = $api->getTransaction('MtD0589C9ADA5CDCE7C21B60FD684BB04AF067261737749B6E18A6B2168A348B89');
//                print_r($response);
//            } catch(RequestException $exception) {
//                var_dump($exception->getResponse()->getBody()->getContents());
//                // handle error
//            }
        }

        sleep(60);
        $output->writeln('SELL 500');
        $txSell = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterSellCoinTx::TYPE,
            'data' => [
                'coinToSell' => $coinSymbol,
                'valueToSell' => '500',
                'coinToBuy' => $mntSymbol,
                'minimumValueToBuy' => 1,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);

//        try {
//            $response = $api->send($txSell->sign($privateKey));
//            print_r($response);
//        } catch (RequestException $exception) {
//            var_dump($exception->getResponse()->getBody()->getContents());
//        }

        //SELL ALL
        $output->writeln('SELL ALL');
        $txSellAll = new MinterTx([
            'nonce' => $api->getNonce($address),
            'chainId' => MinterTx::TESTNET_CHAIN_ID,
            'gasPrice' => 1,
            'gasCoin' => $mntSymbol,
            'type' => MinterSellAllCoinTx::TYPE,
            'data' => [
                'coinToSell' => $coinSymbol,
                'coinToBuy' => $mntSymbol,
                'minimumValueToBuy' => 1,
            ],
            'payload' => '',
            'serviceData' => '',
            'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE // or SIGNATURE_MULTI_TYPE
        ]);

        try {
            $response = $api->send($txSellAll->sign($privateKey));
//            $response = $api->getTransaction("Mt{$response->result->hash}");
            print_r($response);
        } catch (RequestException $exception) {
            var_dump($exception->getResponse()->getBody()->getContents());
            // handle error
        }
    }
}
