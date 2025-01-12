<?php

declare(strict_types = 1);

namespace App\Service;

use App\DTO\ExchangeRateDTO;
use App\Entity\BinanceExchangeRate;
use App\Service\ExchangeRateProvider\ExchangeRateProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ExchangeRateService
{
    public function __construct(
        private EntityManagerInterface        $entityManager,
        private ServiceEntityRepository       $repository,
        private ExchangeRateProviderInterface $provider
    ) {
    }

    public function updateRates(): void
    {
        $rates = $this->provider->fetchRates();

        foreach ($rates as $currencyPair => $rate) {
            $exchangeRate = new BinanceExchangeRate($currencyPair, $rate, new \DateTimeImmutable());
            $this->entityManager->persist($exchangeRate);
        }

        $this->entityManager->flush();
    }

    public function getRatesForPeriodAndPairs(\DateTimeInterface $start, \DateTimeInterface $end, array $pairs): array
    {
        $rates = $this->repository->findByPeriodAndPairs($start, $end, $pairs);

        return array_map(static function (BinanceExchangeRate $rate) {
            return new ExchangeRateDTO(
                $rate->getCurrencyPair(),
                $rate->getRate(),
                $rate->getTimestamp()
            );
        }, $rates);
    }
}