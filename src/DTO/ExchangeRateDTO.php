<?php

declare(strict_types=1);

namespace App\DTO;

class ExchangeRateDTO
{
    public function __construct(
        public string $currencyPair,
        public float $rate,
        public \DateTimeInterface $timestamp
    ) {}
}