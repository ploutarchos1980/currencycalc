<?php

namespace App\Entity;

use App\Repository\CurrencyListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyListRepository::class)]
class CurrencyList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $fromCoin;

    #[ORM\Column(type: 'string', length: 30)]
    private $toCoin;

    #[ORM\Column(type: 'float')]
    private $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCoin(): ?string
    {
        return $this->fromCoin;
    }

    public function setFromCoin(string $fromCoin): self
    {
        $this->fromCoin = $fromCoin;

        return $this;
    }

    public function getToCoin(): ?string
    {
        return $this->toCoin;
    }

    public function setToCoin(string $toCoin): self
    {
        $this->toCoin = $toCoin;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
