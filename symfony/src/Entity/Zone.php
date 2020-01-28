<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ZoneRepository")
 */
class Zone
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @var string
     */
    private $uuid;

    /**
     * @ORM\Column(type="bigint")
     */
    private $osmId;

    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lng;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Polygon", mappedBy="zone")
     */
    private $polygons;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $area;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->polygons = new ArrayCollection();
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getOsmId(): ?string
    {
        return $this->osmId;
    }

    public function setOsmId(string $osmId): self
    {
        $this->osmId = $osmId;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return Collection|Polygon[]
     */
    public function getPolygons(): Collection
    {
        return $this->polygons;
    }

    public function addPolygon(Polygon $polygon): self
    {
        if (!$this->polygons->contains($polygon)) {
            $this->polygons[] = $polygon;
            $polygon->setZone($this);
        }

        return $this;
    }

    public function removePolygon(Polygon $polygon): self
    {
        if ($this->polygons->contains($polygon)) {
            $this->polygons->removeElement($polygon);
            // set the owning side to null (unless already changed)
            if ($polygon->getZone() === $this) {
                $polygon->setZone(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): self
    {
        $this->area = $area;

        return $this;
    }
}
