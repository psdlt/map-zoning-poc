<?php

namespace App\Entity;

use App\Model\LineString;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolygonRepository")
 */
class Polygon
{
    public const TYPE_EXTERIOR = 1;
    public const TYPE_HOLE = 2;

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @var string
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zone", inversedBy="polygons")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     */
    private $zone;

    /**
     * @ORM\Column(type="polygon")
     */
    private $polygon;

    /**
     * @ORM\Column(type="polygon")
     */
    private $simplified;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    private $line;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getPolygon(): LineString
    {
        return $this->polygon;
    }

    public function setPolygon(LineString $polygon): self
    {
        $this->polygon = $polygon;

        return $this;
    }

    public function getSimplified(): LineString
    {
        return $this->simplified;
    }

    public function setSimplified(LineString $simplified): self
    {
        $this->simplified = $simplified;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(?string $line): self
    {
        $this->line = $line;

        return $this;
    }
}
