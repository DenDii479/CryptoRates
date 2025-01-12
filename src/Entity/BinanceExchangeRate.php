<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\BinanceExchangeRateRepository')]
#[ORM\Table(name: 'binance_exchange_rate')]
class BinanceExchangeRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $currencyPair;

    #[ORM\Column(type: 'float')]
    private float $rate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $timestamp;

    public function __construct(string $currencyPair, float $rate, \DateTimeImmutable $timestamp)
    {
        $this->currencyPair = $currencyPair;
        $this->rate = $rate;
        $this->timestamp = $timestamp;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyPair(): string
    {
        return $this->currencyPair;
    }

    public function setCurrencyPair(string $currencyPair): self
    {
        $this->currencyPair = $currencyPair;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}