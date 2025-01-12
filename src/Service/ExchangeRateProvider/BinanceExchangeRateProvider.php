<?php

declare(strict_types = 1);

namespace App\Service\ExchangeRateProvider;

use App\Enum\Binance\BinanceCurrencyPairs;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceExchangeRateProvider implements ExchangeRateProviderInterface
{
    public const API_URL = 'https://api.binance.com/api/v3/ticker/price';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function fetchRates(): array
    {
        try {
            $response = $this->httpClient->request('GET', self::API_URL);
            $ratesData = $response->toArray();

            $rates = [];

            foreach ($ratesData as $rateItem) {
                if (\in_array($rateItem['symbol'], BinanceCurrencyPairs::values(), true)) {
                    $rates[$rateItem['symbol']] = (float) $rateItem['price'];
                }
            }

            return $rates;
        } catch (\Throwable) {
            throw new \Exception('Something went wrong');
        }
    }
}