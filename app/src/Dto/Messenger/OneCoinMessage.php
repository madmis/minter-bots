<?php

namespace App\Dto\Messenger;

/**
 * Class OneCoinMessage
 * @package App\Dto\Messenger
 */
class OneCoinMessage implements MessengerMessageInterface
{
    /**
     * coinId.
     *
     * @var int
     */
    private int $coinId;

    /**
     * OneCoinMessage.
     *
     * @param int $coinId
     *
     * @return void
     */
    public function __construct(int $coinId)
    {
        $this->coinId = $coinId;
    }

    /**
     * @return int
     */
    public function getCoinId() : int
    {
        return $this->coinId;
    }
}
