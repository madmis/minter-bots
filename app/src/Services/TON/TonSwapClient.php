<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Services\TON;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;

/**
 * Class TonSwapClient.
 * @package App\Services\TON
 */
class TonSwapClient
{
    /**
     * client.
     *
     * @var ClientInterface|Client
     */
    private ClientInterface $client;

    /**
     * standardHeaders.
     *
     * @var array
     */
    private array $standardHeaders = [
        'DNT' => 1,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
        'Referer' => 'https://tonswap.io/',
        'Origin' => 'https://tonswap.io',
        'Host' => 'ton-swap-indexer-test.broxus.com',
    ];

    /**
     * TonSwapClient.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * getCurrencies.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getCurrencies(int $limit = 20, int $offset = 0) : array
    {
        $response = $this->client->post('https://ton-swap-indexer-test.broxus.com/v1/currencies', [
            RequestOptions::HEADERS => array_merge($this->standardHeaders, []),
            RequestOptions::JSON => [
                'currencyAddresses' => [],
                'limit' => $limit,
                'offset' => $offset,
                'ordering' => 'tvldescending',
                'whiteListUri' => 'https://raw.githubusercontent.com/broxus/ton-assets/master/manifest.json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * getPairs.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getPairs(int $limit = 20, int $offset = 0) : array
    {
        $response = $this->client->post('https://ton-swap-indexer-test.broxus.com/v1/pairs', [
            RequestOptions::HEADERS => array_merge($this->standardHeaders, []),
            RequestOptions::JSON => [
                'currencyAddresses' => [],
                'limit' => $limit,
                'offset' => $offset,
                'ordering' => 'tvldescending',
                'whiteListUri' => 'https://raw.githubusercontent.com/broxus/ton-assets/master/manifest.json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * getPoolInfo.
     *
     * @param string $poolAddress
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getPoolInfo(string $poolAddress) : array
    {
        $response = $this->client->post("https://ton-swap-indexer-test.broxus.com/v1/pairs/address/{$poolAddress}", [
            RequestOptions::HEADERS => array_merge($this->standardHeaders, []),
            RequestOptions::JSON => [],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * getPoolsTransactions.
     *
     * @param string $poolAddress
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getPoolsTransactions(string $poolAddress, int $limit = 10, int $offset = 0) : array
    {
        $response = $this->client->post('https://ton-swap-indexer-test.broxus.com/v1/transactions', [
            RequestOptions::HEADERS => array_merge($this->standardHeaders, []),
            RequestOptions::JSON => [
                'limit' => $limit,
                'offset' => $offset,
                'ordering' => 'blocktimedescending',
                'poolAddress' => $poolAddress,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * getManifest.
     *
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getManifest() : array
    {
        $response = $this->client->get('https://raw.githubusercontent.com/broxus/ton-assets/master/manifest.json');
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data;

    }
}
