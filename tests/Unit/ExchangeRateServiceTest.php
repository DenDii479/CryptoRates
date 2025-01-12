<?php

declare(strict_types = 1);

namespace App\Tests\Unit;

use App\DTO\ExchangeRateDTO;
use App\Entity\BinanceExchangeRate;
use App\Service\ExchangeRateProvider\ExchangeRateProviderInterface;
use App\Service\ExchangeRateService;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ServiceEntityRepository $repository;
    private ExchangeRateProviderInterface $provider;
    private ExchangeRateService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(ServiceEntityRepository::class);
        $this->provider = $this->createMock(ExchangeRateProviderInterface::class);

        $this->service = new ExchangeRateService(
            $this->entityManager,
            $this->repository,
            $this->provider
        );
    }

    public function testUpdateRatesSuccess(): void
    {
        $rates = [
            'BTCUSDT' => 35000.0,
            'ETHBTC'  => 0.03447,
        ];

        $this->provider
            ->method('fetchRates')
            ->willReturn($rates);

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('persist')
            ->with($this->isInstanceOf(BinanceExchangeRate::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->service->updateRates();
    }

    public function testUpdateRatesEmptyRates(): void
    {
        $this->provider
            ->method('fetchRates')
            ->willReturn([]);

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->service->updateRates();
    }

    public function testUpdateRatesThrowsException(): void
    {
        $this->provider
            ->method('fetchRates')
            ->willReturn(['BTCUSDT' => 35000.0]);

        $this->entityManager
            ->method('persist')
            ->willThrowException(new \Exception('Database error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->service->updateRates();
    }

    public function testGetRatesForPeriodAndPairsSuccess(): void
    {
        $start = new DateTimeImmutable('2023-01-01');
        $end = new DateTimeImmutable('2023-01-31');
        $pairs = ['BTCUSDT', 'ETHBTC'];

        $binanceRate = new BinanceExchangeRate('BTCUSDT', 35000.0, new DateTimeImmutable('2023-01-15'));
        $this->repository
            ->method('findByPeriodAndPairs')
            ->with($start, $end, $pairs)
            ->willReturn([$binanceRate]);

        $result = $this->service->getRatesForPeriodAndPairs($start, $end, $pairs);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ExchangeRateDTO::class, $result[0]);
        $this->assertSame('BTCUSDT', $result[0]->getCurrencyPair());
        $this->assertSame(35000.0, $result[0]->getRate());
        $this->assertEquals(new DateTimeImmutable('2023-01-15'), $result[0]->getTimestamp());
    }

    public function testGetRatesForPeriodAndPairsEmptyResult(): void
    {
        $start = new DateTimeImmutable('2023-01-01');
        $end = new DateTimeImmutable('2023-01-31');
        $pairs = ['BTCUSDT', 'ETHBTC'];

        $this->repository
            ->method('findByPeriodAndPairs')
            ->with($start, $end, $pairs)
            ->willReturn([]);

        $result = $this->service->getRatesForPeriodAndPairs($start, $end, $pairs);

        $this->assertEmpty($result);
    }
}
