<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PricingRepository")
 */
class Pricing
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @var string
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zone")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $fromZone;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zone")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $toZone;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function getUuid(): ?int
    {
        return $this->uuid;
    }

    public function getFromZone(): ?Zone
    {
        return $this->fromZone;
    }

    public function setFromZone(?Zone $fromZone): self
    {
        $this->fromZone = $fromZone;

        return $this;
    }

    public function getToZone(): ?Zone
    {
        return $this->toZone;
    }

    public function setToZone(?Zone $toZone): self
    {
        $this->toZone = $toZone;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
