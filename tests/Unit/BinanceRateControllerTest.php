<?php

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\Controller\Api\BinanceRateController;
use App\DTO\ExchangeRateDTO;
use App\Service\ExchangeRateService;
use App\Validator\BinanceDateRangeValidator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class BinanceRateControllerTest extends TestCase
{
    private BinanceRateController $controller;
    private $exchangeRateService;
    private $validator;

    protected function setUp(): void
    {
        $this->exchangeRateService = $this->createMock(ExchangeRateService::class);
        $this->validator = $this->createMock(BinanceDateRangeValidator::class);
        $this->controller = new BinanceRateController($this->exchangeRateService, $this->validator);
    }

    public function testGetRatesSuccess(): void
    {
        $request = new Request([
            'start_date'     => '2023-01-01',
            'end_date'       => '2023-01-31',
            'currency_pairs' => 'BTCUSDT,BTCEUR',
        ]);

        $this->validator
            ->method('validate')
            ->willReturn(true);

        $dto = new ExchangeRateDTO('BTCUSDT', 35000.0, new DateTimeImmutable('2023-01-01T00:00:00+00:00'));

        $this->exchangeRateService
            ->method('getRatesForPeriodAndPairs')
            ->willReturn([$dto]);

        $response = $this->controller->getRates($request);
        $this->assertSame(200, $response->getStatusCode());

        $expectedResponse = [
            [
                'currencyPair' => 'BTCUSDT',
                'rate'         => 35000.0,
                'timestamp'    => '2023-01-01T00:00:00+00:00',
            ],
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expectedResponse), $response->getContent());
    }

    public function testValidationError(): void
    {
        $request = new Request([
            'start_date'     => '',
            'end_date'       => '',
            'currency_pairs' => '',
        ]);

        $this->validator
            ->method('validate')
            ->willReturn('Invalid date range or currency pairs');

        $response = $this->controller->getRates($request);

        $this->assertSame(400, $response->getStatusCode());

        $expectedResponse = [
            'status'  => 'error',
            'message' => 'Invalid date range or currency pairs',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expectedResponse), $response->getContent());
    }

    public function testEmptyResult(): void
    {
        $request = new Request([
            'start_date'     => '2023-01-01',
            'end_date'       => '2023-01-31',
            'currency_pairs' => 'BTCUSDT,BTCEUR',
        ]);

        $this->validator
            ->method('validate')
            ->willReturn(true);

        $this->exchangeRateService
            ->method('getRatesForPeriodAndPairs')
            ->willReturn([]);

        $response = $this->controller->getRates($request);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([]), $response->getContent());
    }
}
