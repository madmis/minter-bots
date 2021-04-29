<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PoolsCommand
 * @package App\Command
 */
class PoolsCommand extends Command
{
    /**
     * defaultName.
     *
     * @var string
     */
    protected static $defaultName = 'app:pools';

    /**
     * configure.
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setDescription('Get pools info.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $client = new Client();
        $items = [];

        do {
            $poolsUrl = $data['links']['next'] ?? 'https://explorer-api.minter.network/api/v2/pools';
            $output->writeln("Request page: {$poolsUrl}");
            $response = $client->get($poolsUrl);
            $content = $response->getBody()->getContents();
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $items = array_merge($items, $data['data']);
        } while (!empty($data['links']['next']));

        if ($items) {
            usort($items, static function (array $b, array $a) {
                $aV = (int) $a['trade_volume_bip_30d'];
                $bV = (int) $b['trade_volume_bip_30d'];
                if ($aV === $bV) {
                    return 0;
                }

                return ($aV < $bV) ? -1 : 1;
            });
        }

        foreach ($items as $item) {
            $volume = (int) $item['trade_volume_bip_30d'];

//            if ($volume > 1000) {
                $output->writeln(sprintf(
                    '%s: %s/%s - %s',
                    $item['token']['symbol'],
                    $item['coin0']['symbol'],
                    $item['coin1']['symbol'],
                    $volume,
                ));
//            }
        }

        return 0;
    }
}
