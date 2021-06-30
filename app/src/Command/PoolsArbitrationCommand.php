<?php

namespace App\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Minter\MinterAPI;
use Minter\SDK\MinterCoins\MinterSellSwapPoolTx;
use Minter\SDK\MinterTx;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PoolsArbitrationCommand
 * @package App\Command
 */
class PoolsArbitrationCommand extends Command
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
        0 => [
            'wallet' => 'Mx3d6927d293a446451f050b330aee443029be1564',
            'pk' => '76ec6fbe9a73ce052559af62518db8d91deda9bdda5fd213ab911be3e0a546dd',
        ],
        1 => [
            'wallet' => 'Mxa7de32768daa3e3d3273b9e251e424be33858cfa',
            'pk' => '4d09292487ba49d2b53b3d2685d77569341d4e02e4a6fcc3e621556aa37a3677',
        ],
        2 => [
            'wallet' => 'Mx8ab4f4f3909182e1dd5bebf239a043960e4e4557',
            'pk' => '30fbeff069a78b69afe75e2b43459af4674cf915b3799a57bb739f236d151f88',
        ],
        3 => [
            'wallet' => 'Mx7586ad025e0f6665c28528f6844ddd00185d097c',
            'pk' => 'da708822753c1c3f054c1e63d0667fcb7b9e2beb59930880905789c6d82e6025',
        ],
    ];

    /**
     * logger.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * PollsArbitrationCommand.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();

        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Arbitrate in Minter pools.')
            // 'https://mnt.funfasy.dev/v2/' - this node has a very low request limit. It's require min 3 sec delay;
            // https://mnt.funfasy.dev/v0.2/ - req delay 2500000
            ->addOption(
                'read-node',
                null,
                InputOption::VALUE_REQUIRED,
                'Minter read node url',
                'https://api.minter.one/v2/'
            )
            ->addOption(
                'write-node',
                null,
                InputOption::VALUE_REQUIRED,
                'Minter write node url',
                'https://gate-api.minter.network/api/v2/'
            )
            ->addOption('req-delay', null, InputOption::VALUE_REQUIRED, 'Delay between requests in microseconds', 500000)
            ->addOption('tx-amount', null, InputOption::VALUE_REQUIRED, 'Transaction amount', 300)
            ->addOption('pool-idx', null, InputOption::VALUE_REQUIRED, 'Pool index', 0)
//            ->addOption(
//                'wallet-idx',
//                null,
//                InputOption::VALUE_REQUIRED,
//                'Wallet index: 0(Mx3d...1564), 1(Mxa7...8cfa), 2(Mx8a...4557), 3(Mx75...097c)',
//                0
//            )
        ;
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
        $ftmusdId = 1048;
        $minterPayId = 133;
        $microbId = 1087;
        $latteinId = 1036;
        $freedomId = 21;
        $hubId = 1902;
        $liquidhubId = 1893;
        $monsterHUBId = 1895;
        $hubabubaId = 1942;
        $capId = 1934;
        $monehubId = 1901;
        $hubchainId = 1900;
        $trustHubId = 1903;
        $academicId = 122;
        $imperialId = 1937;
        $tickers = [
            $bipId => 'BIP',
            $bigmacId => 'BIGMAC',
            $usdxId => 'USDX',
            $quotaId => 'QUOTA',
            $couponId => 'COUPON',
            $ftmusdId => 'FTMUSD',
            $minterPayId => 'MINTERPAY',
            $microbId => 'MICROB',
            $latteinId => 'LATTEIN',
            $freedomId => 'FREEDOM',
            $hubId => 'HUB',
            $liquidhubId => 'LIQUIDHUB',
            $monsterHUBId => 'MonsterHUB',
            $hubabubaId => 'HUBABUBA',
            $capId => 'CAP',
            $monehubId => 'MONEHUB',
            $hubchainId => 'HUBCHAIN',
            $trustHubId => 'TRUSTHUB',
            $academicId => 'ACADEMIC',
            $imperialId => 'IMPERIAL',
        ];
        $pools = [
            0 => [
                [$bipId, $hubId, $usdxId, $bipId],
                [$bipId, $usdxId, $hubId, $bipId],
                [$bipId, $hubId, $liquidhubId, $bipId],
                [$bipId, $liquidhubId, $hubId, $bipId],
                [$bipId, $hubId, $monsterHUBId, $bipId],
                [$bipId, $monsterHUBId, $hubId, $bipId],
                [$bipId, $hubabubaId, $hubId, $bipId],
                [$bipId, $hubId, $hubabubaId, $bipId],
                [$bipId, $hubId, $capId, $bipId],
                [$bipId, $capId, $hubId, $bipId],
                [$bipId, $monehubId, $hubId, $bipId],
                [$bipId, $hubId, $monehubId, $bipId],
                [$bipId, $hubId, $hubchainId, $bipId],
                [$bipId, $hubchainId, $hubId, $bipId],
                [$bipId, $academicId, $hubId, $bipId],
                [$bipId, $hubId, $academicId, $bipId],
                [$bipId, $imperialId, $hubId, $bipId],
            ],
            1 => [
                [$bipId, $liquidhubId, $hubId, $monsterHUBId, $bipId],
                [$bipId, $liquidhubId, $hubId, $usdxId, $bipId],
                [$bipId, $quotaId, $usdxId, $hubId, $bipId],
                [$bipId, $couponId, $usdxId, $hubId, $bipId],
                [$bipId, $hubabubaId, $capId, $hubId, $bipId],
                [$bipId, $capId, $hubabubaId, $hubId, $bipId],
                [$bipId, $hubId, $capId, $hubabubaId, $bipId],
                [$bipId, $liquidhubId, $hubId, $monehubId, $bipId],
            ],
            2 => [
                [$bipId, $bigmacId, $couponId, $bipId],
                [$bipId, $couponId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $quotaId, $bipId],
                [$bipId, $quotaId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $quotaId, $bipId],
                [$bipId, $quotaId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $couponId, $bipId],
                [$bipId, $couponId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $usdxId, $bipId],
                [$bipId, $usdxId, $bigmacId, $bipId],
                [$bipId, $quotaId, $usdxId, $bipId],
                [$bipId, $usdxId, $quotaId, $bipId],
                [$bipId, $usdxId, $couponId, $bipId],
                [$bipId, $couponId, $usdxId, $bipId],
                [$bipId, $microbId, $usdxId, $bipId],

                [$bipId, $bigmacId, $usdxId, $couponId, $bipId],
                [$bipId, $bigmacId, $couponId, $usdxId, $bipId],
                [$bipId, $usdxId, $bigmacId, $couponId, $bipId],
                [$bipId, $usdxId, $couponId, $bigmacId, $bipId],
            ],
            3 => [
                [$bipId, $usdxId, $microbId, $bipId],
                [$bipId, $ftmusdId, $usdxId, $bipId],
                [$bipId, $usdxId, $ftmusdId, $bipId],
                [$bipId, $usdxId, $latteinId, $bipId],
                [$bipId, $latteinId, $usdxId, $bipId],
                [$bipId, $freedomId, $ftmusdId, $bipId],
                [$bipId, $ftmusdId, $freedomId, $bipId],

                [$bipId, $bigmacId, $usdxId, $quotaId, $bipId],
                [$bipId, $quotaId, $usdxId, $bigmacId, $bipId],
                [$bipId, $quotaId, $bigmacId, $usdxId, $bipId],
                [$bipId, $bigmacId, $quotaId, $usdxId, $bipId],
                [$bipId, $couponId, $usdxId, $quotaId, $bipId],
                [$bipId, $quotaId, $usdxId, $couponId, $bipId],
                [$bipId, $couponId, $bigmacId, $usdxId, $bipId],
                [$bipId, $couponId, $usdxId, $bigmacId, $bipId],
            ],
            4 => [
                [$bipId, $bigmacId, $couponId, $bipId],
                [$bipId, $couponId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $quotaId, $bipId],
                [$bipId, $quotaId, $bigmacId, $bipId],

                [$bipId, $hubId, $liquidhubId, $bipId],
                [$bipId, $liquidhubId, $hubId, $bipId],
                [$bipId, $bigmacId, $usdxId, $bipId],
                [$bipId, $usdxId, $bigmacId, $bipId],
                [$bipId, $quotaId, $usdxId, $bipId],
                [$bipId, $usdxId, $quotaId, $bipId],
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
                [$bipId, $quotaId, $bigmacId, $bipId],
                [$bipId, $bigmacId, $couponId, $bipId],
                [$bipId, $couponId, $bigmacId, $bipId],
                [$bipId, $hubId, $liquidhubId, $bipId],
                [$bipId, $liquidhubId, $hubId, $bipId],
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
            ]
        ];

        $readNodeUrl = $input->getOption('read-node');
        $writeNodeUrl = $input->getOption('write-node');
        $txAmount = (int) $input->getOption('tx-amount');
        $reqDelay = (int) $input->getOption('req-delay');
        $poolIdx = (int) $input->getOption('pool-idx');

        $readClient = new Client([
            'base_uri' => $readNodeUrl,
            'connect_timeout' => 15.0,
            'timeout' => 30.0,
            //            'proxy' => '159.8.114.34:8123',
        ]);
        $writeClient = new Client([
            'base_uri' => $writeNodeUrl,
            'connect_timeout' => 15.0,
            'timeout' => 30.0,
            //            'proxy' => '159.8.114.34:8123',
        ]);

        $readApi = new MinterAPI($readClient);
        $writeApi = new MinterAPI($writeClient);
        $walletIdx = 0;
        $walletAddress = $this->wallets[$walletIdx]['wallet'];
        $walletPk = $this->wallets[$walletIdx]['pk'];

        while (true) {
            foreach ($pools[$poolIdx] as $route) {
                $routeStr = implode('=>', array_map(static fn(int $id) => $tickers[$id], $route));
                try {
                    try {
                        $signedTx = $this->signTx($route, $txAmount, $readApi, $walletAddress, $walletPk);
                        $response = (array) $writeApi->send($signedTx);
                        $this->logger->info(sprintf('Route: %s', $routeStr));
                        $this->logger->info("\t", ['response' => (array) $response]);
                        $this->logger->info("\t", ['block' => $readApi->getStatus()->latest_block_height]);

                        // after successful tx change wallet to make new tx from new wallet
                        $walletIdx = $this->getNextWalletIdx($walletIdx);
                        $oldWallet = $walletAddress;
                        $walletAddress = $this->wallets[$walletIdx]['wallet'];
                        $walletPk = $this->wallets[$walletIdx]['pk'];
                        $this->logger->info(sprintf(
                            "\tChange wallet from %s => to: %s: %s",
                            $oldWallet,
                            $walletIdx,
                            $walletAddress,
                        ));

                        usleep($reqDelay);
                    } catch (ClientException $e) {
                        $response = $e->getResponse();
                        $content = $response->getBody()->getContents();
                        /** @noinspection PhpUnhandledExceptionInspection */
                        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                        if (!empty($data['error']['code']) && (int) $data['error']['code'] === 302) {
                            $errorData = $data['error']['data'];
                            $this->logger->debug(sprintf(
                                'Want: %s. Got: %s. Coin: %s',
                                $errorData['maximum_value_to_sell'],
                                $errorData['needed_spend_value'],
                                $errorData['coin_symbol'],
                            ));
                        } else {
                            $this->logger->error($e->getMessage(), [
                                'content' => $e->getResponse()->getBody()->getContents(),
                                'class' => $e::class,
                                'file' => $e->getFile(),
                                'code' => $e->getLine(),
                            ]);
                        }

                        usleep($reqDelay);
                    }
                } catch (ServerException $e) {
                    $this->logger->critical(sprintf(
                        '%s: %s',
                        $e->getResponse()->getStatusCode(),
                        $e->getResponse()->getReasonPhrase(),
                    ));
                    sleep(2);
                } catch (GuzzleException $e) {
                    $this->logger->critical($e->getMessage(), [
                        'class' => $e::class,
                        'file' => $e->getFile(),
                        'code' => $e->getLine(),
                    ]);

                    sleep(10);
                }
            }

            sleep(2);
        }

        return self::SUCCESS;
    }

    /**
     * getNextWallet.
     *
     * @param int $currWalletIdx
     *
     * @return int
     */
    private function getNextWalletIdx(int $currWalletIdx) : int
    {
        return isset($this->wallets[$currWalletIdx + 1]) ? $currWalletIdx + 1 : 0;
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
