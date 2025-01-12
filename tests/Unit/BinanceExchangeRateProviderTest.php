<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\ExchangeRateProvider\BinanceExchangeRateProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BinanceExchangeRateProviderTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private BinanceExchangeRateProvider $provider;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->provider = new BinanceExchangeRateProvider($this->httpClient);
    }

    public function testFetchRatesSuccess(): void
    {
        $apiResponse = [
            ['symbol' => 'BTCUSDT', 'price' => '35000.00'],
            ['symbol' => 'ETHBTC', 'price' => '0.03447000'],
            ['symbol' => 'BTCEUR', 'price' => '33000.00'],
        ];

        $expectedRates = [
            'BTCUSDT' => 35000.0,
            'BTCEUR' => 33000.0,
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn($apiResponse);

        $this->httpClient
            ->method('request')
            ->with('GET', BinanceExchangeRateProvider::API_URL)
            ->willReturn($response);

        $actualRates = $this->provider->fetchRates();

        $this->assertSame($expectedRates, $actualRates);
    }

    public function testFetchRatesEmptyResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([]);

        $this->httpClient
            ->method('request')
            ->with('GET', BinanceExchangeRateProvider::API_URL)
            ->willReturn($response);

        $actualRates = $this->provider->fetchRates();

        $this->assertSame([], $actualRates);
    }

    public function testFetchRatesHttpClientError(): void
    {
        $this->httpClient
            ->method('request')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->provider->fetchRates();
    }
}
