<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BinanceExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BinanceExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceExchangeRate::class);
    }

    public function findByPeriodAndPairs(\DateTimeInterface $start, \DateTimeInterface $end, array $pairs): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.timestamp BETWEEN :start AND :end')
            ->andWhere('e.currencyPair IN (:pairs)')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('pairs', $pairs)
            ->getQuery()
            ->getResult();
    }
}