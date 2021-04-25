<?php

namespace App\Dto;

use JetBrains\PhpStorm\Pure;

/**
 * Class CoinDto
 * @package App\Dto
 */
class CoinDto
{
    /**
     * id.
     *
     * @var int
     */
    private int $id;

    /**
     * symbol.
     *
     * @var string
     */
    private string $symbol;

    /**
     * CoinDto.
     *
     * @param int $id
     * @param string $symbol
     *
     * @return void
     */
    public function __construct(int $id, string $symbol)
    {
        $this->id = $id;
        $this->symbol = strtoupper($symbol);
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSymbol() : string
    {
        return $this->symbol;
    }

    /**
     * equals.
     *
     * @param CoinDto $coin
     *
     * @return bool
     */
    #[Pure] public function equals(CoinDto $coin) : bool
    {
        return $coin->getId() === $this->id;
    }
}
