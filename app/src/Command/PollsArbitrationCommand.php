<?php

namespace App\Command;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterTx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PollsArbitrationCommand
 * @package App\Command
 */
class PollsArbitrationCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:pool:arbitrate';

    /**
     * wallets.
     *
     * @var string[][]
     */
    private array $wallets = [
        [
            'wallet' => 'Mx3d6927d293a446451f050b330aee443029be1564',
            'pk' => '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd',
        ],
        [
            'wallet' => 'Mxa7de32768daa3e3d3273b9e251e424be33858cfa',
            'pk' => '4d09292487ba49d2b53b3d2685d77569341d4e02e4a6fcc3e621556aa37a3677',
        ],
        [
            'wallet' => 'Mx8ab4f4f3909182e1dd5bebf239a043960e4e4557',
            'pk' => '30fbeff069a78b69afe75e2b43459af4674cf915b3799a57bb739f236d151f88',
        ],
        [
            'wallet' => 'Mx7586ad025e0f6665c28528f6844ddd00185d097c',
            'pk' => 'da708822753c1c3f054c1e63d0667fcb7b9e2beb59930880905789c6d82e6025',
        ],
    ];

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Arbitrate in Minter pools.')
            // 'https://mnt.funfasy.dev/v2/' - this node has a very low request limit. It's require min 3 sec delay;
            ->addOption('node-url', null, InputOption::VALUE_REQUIRED, 'Minter node url', 'https://api.minter.one/v2/')
            ->addOption('req-delay', null, InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 300000)
            ->addOption('tx-amount', null, InputOption::VALUE_REQUIRED, 'Transaction amount', 300)
            ->addOption(
                'wallet-idx',
                null,
                InputOption::VALUE_REQUIRED,
                'Wallet index: 0(Mx3d...1564), 1(Mxa7...8cfa), 2(Mx8a...4557), 3(Mx75...097c)',
                0
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $bipId = 0;
        $bigmacId = 907;
        $usdxId = 1678;
        $quotaId = 1086;
        $couponId = 1043;
        $rubxId = 1784;
        $ftmusdId = 1048;
        $minterPayId = 133;
        $microbId = 1087;
        $latteinId = 1036;
        $freedomId = 21;
        $tickers = [
            $bipId => 'BIP',
            $bigmacId => 'BIGMAC',
            $usdxId => 'USDX',
            $quotaId => 'QUOTA',
            $couponId => 'COUPON',
            $rubxId => 'RUBX',
            $ftmusdId => 'FTMUSD',
            $minterPayId => 'MINTERPAY',
            $microbId => 'MICROB',
            $latteinId => 'LATTEIN',
            $freedomId => 'FREEDOM',
        ];
        $poolsToCheck = [
            // fee 2 BIP
            [$bipId, $bigmacId, $couponId, $bipId],
            [$bipId, $couponId, $bigmacId, $bipId],
            [$bipId, $bigmacId, $usdxId, $bipId],
            [$bipId, $usdxId, $bigmacId, $bipId],
            [$bipId, $quotaId, $usdxId, $bipId],
            [$bipId, $usdxId, $quotaId, $bipId],
            [$bipId, $rubxId, $usdxId, $bipId],
            [$bipId, $usdxId, $rubxId, $bipId],
            [$bipId, $usdxId, $couponId, $bipId],
            [$bipId, $couponId, $usdxId, $bipId],
            [$bipId, $microbId, $usdxId, $bipId],
            [$bipId, $usdxId, $microbId, $bipId],
            [$bipId, $ftmusdId, $usdxId, $bipId],
            [$bipId, $usdxId, $ftmusdId, $bipId],
            [$bipId, $usdxId, $latteinId, $bipId],
            [$bipId, $latteinId, $usdxId, $bipId],
            [$bipId, $freedomId, $ftmusdId, $bipId],
            [$bipId, $ftmusdId, $freedomId, $bipId],
            [$bipId, $bigmacId, $quotaId, $bipId],
            [$bipId, $quotaId, $bigmacId,  $bipId],
            // fee 2.25 BIP
            [$bipId, $bigmacId, $usdxId, $quotaId, $bipId],
            [$bipId, $quotaId, $usdxId, $bigmacId, $bipId],
            [$bipId, $quotaId, $bigmacId, $usdxId, $bipId],
            [$bipId, $bigmacId, $quotaId, $usdxId, $bipId],
            [$bipId, $couponId, $usdxId, $quotaId, $bipId],
            [$bipId, $quotaId, $usdxId, $couponId, $bipId],
            [$bipId, $usdxId, $bigmacId, $couponId, $bipId],
            [$bipId, $usdxId, $couponId, $bigmacId, $bipId],
            [$bipId, $bigmacId, $usdxId, $couponId, $bipId],
            [$bipId, $bigmacId, $couponId, $usdxId, $bipId],
            [$bipId, $couponId, $bigmacId, $usdxId, $bipId],
            [$bipId, $couponId, $usdxId, $bigmacId, $bipId],
            [$bipId, $couponId, $usdxId, $rubxId, $bipId],
            [$bipId, $rubxId, $usdxId, $couponId, $bipId],

        ];
        $nodeUrl = $input->getOption('node-url');
        $txAmount = (int) $input->getOption('tx-amount');
        $reqDelay = (int) $input->getOption('req-delay');
        $api = new MinterAPI($nodeUrl);
        $walletIdx = (int) $input->getOption('wallet-idx');
        $walletAddress = $this->wallets[$walletIdx]['wallet'];
        $walletPk = $this->wallets[$walletIdx]['pk'];

        while (true) {
            foreach ($poolsToCheck as $route) {
                try {
                    try {
                        $signedTx = $this->signTx($route, $txAmount, $api, $walletAddress, $walletPk);
                        $response = $api->send($signedTx);
                        $output->writeln(sprintf(
                            'R: %s',
                            implode('=>', array_map(static fn(int $id) => $tickers[$id], $route))
                        ));
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $output->writeln(sprintf("    Response: %s", json_encode($response, JSON_THROW_ON_ERROR)));
                    } catch (ClientException $e) {
                        $response = $e->getResponse();
                        $content = $response->getBody()->getContents();
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                        if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                            $errorData = $data['error']['data'];
                            $output->writeln(sprintf(
                                "    Want: %s. Got: %s. Coin: %s",
                                $errorData['maximum_value_to_sell'],
                                $errorData['needed_spend_value'],
                                $errorData['coin_symbol'],
                            ), $output::VERBOSITY_VERBOSE);
                        } else {
                            $msg = json_encode([
                                'content' => $e->getResponse()->getBody()->getContents(),
                                'message' => $e->getMessage(),
                                'class' => $e::class,
                                'file' => $e->getFile(),
                                'code' => $e->getLine(),
                            ], JSON_THROW_ON_ERROR);
                            $output->writeln("!!!ERROR: {$msg}");
                        }
                    }
                } catch (GuzzleException $e) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $msg = json_encode([
                        'message' => $e->getMessage(),
                        'class' => $e::class,
                        'file' => $e->getFile(),
                        'code' => $e->getLine(),
                    ], JSON_THROW_ON_ERROR);
                    $output->writeln(sprintf("!!!CRITICAL: %s", $msg));

                    sleep(60);
                }

                usleep($reqDelay);
            }

            sleep(2);
        }

        return self::SUCCESS;
    }

    /**
     * signTx.
     *
     * @param array $route
     * @param int $txAmount
     * @param MinterAPI $api
     * @param string $walletAddress
     * @param string $walletPk
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function signTx(
        array $route,
        int $txAmount,
        MinterAPI $api,
        string $walletAddress,
        string $walletPk
    ) : string {
        $fee = count($route) === 4 ? 2 : 2.5;
        $data = new MinterSellSwapPoolTx($route, $txAmount, $txAmount + $fee);
        $nonce = $api->getNonce($walletAddress);
        $tx = new MinterTx($nonce, $data);

        return $tx->sign($walletPk);
    }
}
