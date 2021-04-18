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
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Arbitrate in Minter pools.')
            ->addOption('node-url', null, InputOption::VALUE_REQUIRED, 'Minter node url', 'https://api.minter.one/v2/')
            ->addOption('tx-amount', null, InputOption::VALUE_REQUIRED, 'Transaction amount', 300);
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
            // fee 2.25 BIP
            [$bipId, $bigmacId, $usdxId, $quotaId, $bipId],
            [$bipId, $quotaId, $usdxId, $bigmacId, $bipId],
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
        $api = new MinterAPI($nodeUrl);
        $walletAddress = 'Mx3d6927d293a446451f050b330aee443029be1564';
        $walletPk = '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd';

        while (true) {
            foreach ($poolsToCheck as $route) {
                try {
                    $fee = count($route) === 4 ? 2 : 2.5;
                    $data = new MinterSellSwapPoolTx($route, $txAmount, $txAmount + $fee);
                    $nonce = $api->getNonce($walletAddress);
                    $tx = new MinterTx($nonce, $data);
                    $signedTx = $tx->sign($walletPk);

                    try {
                        $response = $api->send($signedTx);
                        $output->writeln(sprintf(
                            'R: %s',
                            implode('=>', array_map(static fn(int $id) => $tickers[$id], $route))
                        ));
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $output->writeln("    Response: %s", json_encode($response, JSON_THROW_ON_ERROR));
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
                            $output->writeln("!!!ERROR: %s", $e->getResponse()->getBody()->getContents());
                        }
                    }
                } catch (GuzzleException $e) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $output->writeln("!!!CRITICAL: %s", json_encode(
                        [
                            'message' => $e->getMessage(),
                            'class' => $e::class,
                            'file' => $e->getFile(),
                            'code' => $e->getLine(),
                        ],
                        JSON_THROW_ON_ERROR
                    ));
                    sleep(60);
                }

                usleep(500000);
            }

            sleep(2);
        }

        return 0;
    }
}
