<?php

declare(strict_types=1);

namespace App\Service\ExchangeRateProvider;

interface ExchangeRateProviderInterface
{
    /**
     * @return array<string, float>
     */
    public function fetchRates(): array;
}