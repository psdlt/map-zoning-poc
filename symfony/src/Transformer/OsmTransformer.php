<?php

namespace App\Transformer;

use App\Entity\Polygon;
use App\Entity\Zone;
use App\Model\LineString;
use App\Model\Zone as ZoneModel;
use App\Model\ZoneDetailed;
use App\Service\RamerDouglasPeucker;
use App\Service\TurfService;
use Ramsey\Uuid\Uuid;

class OsmTransformer
{
    private const SIMPLIFICATION_EPSILON = 0.01;
    private const CHAPAYEV_CONSTANT = 100;
    private $rdp;
    private $turf;

    public function __construct(RamerDouglasPeucker $rdp, TurfService $turf)
    {
        $this->rdp = $rdp;
        $this->turf = $turf;
    }

    public function toZone($posted)
    {
        $zone = new Zone();
        $zone->setOsmId($posted->osm_id);
        $zone->setLat($posted->lat);
        $zone->setLng($posted->lon);
        $zone->setName($posted->display_name);

        // parse geoJSON
        if ($posted->geojson->type === 'MultiPolygon') {
            foreach ($posted->geojson->coordinates as $poly) {
                $parsed = $this->parsePolygon($poly);
                foreach ($parsed as $polygon) {
                    $zone->addPolygon($polygon);
                }
            }
        }
        if ($posted->geojson->type === 'Polygon') {
            $parsed = $this->parsePolygon($posted->geojson->coordinates);
            foreach ($parsed as $polygon) {
                $zone->addPolygon($polygon);
            }
        }

        $zone->setArea((int)$this->turf->zoneArea($zone));

        return $zone;
    }

    public function toModel(Zone $zone): ZoneModel
    {
        $lines = [];
        $polys = $zone->getPolygons()->toArray();

        usort($polys, function (Polygon $left, Polygon $right) {
            if ($left->getType() === $right->getType()) {
                return 0;
            }

            return $left->getType() === Polygon::TYPE_EXTERIOR ? -1 : 1;
        });

        foreach ($polys as $polygon) {
            if (!array_key_exists($polygon->getLine(), $lines)) {
                $lines[$polygon->getLine()] = [];
            }

            $lines[$polygon->getLine()][] = array_map(function ($point) {
                return [$point[1], $point[0]];
            }, $polygon->getSimplified()->points);
        }

        $type = 'Polygon';

        $count = count($lines);
        if ($count === 1) {
            $lines = array_pop($lines);
        }

        if ($count > 1) {
            $type = 'MultiPolygon';
            $lines = array_values($lines);
        }

        $geoJson = [
            'type' => $type,
            'coordinates' => $lines,
        ];

        return new ZoneModel($zone->getUuid(), $geoJson, $zone->getName());
    }

    public function toDetailedModel(Zone $zone): ZoneDetailed
    {
        $lines = [];
        $polys = $zone->getPolygons()->toArray();

        usort($polys, function (Polygon $left, Polygon $right) {
            if ($left->getType() === $right->getType()) {
                return 0;
            }

            return $left->getType() === Polygon::TYPE_EXTERIOR ? -1 : 1;
        });
        foreach ($polys as $polygon) {
            if (!array_key_exists($polygon->getLine(), $lines)) {
                $lines[$polygon->getLine()] = [];
            }

            $lines[$polygon->getLine()][] = $polygon->getPolygon()->points;
        }

        $polygons = [];
        foreach ($lines as $line => $points) {
            $polygons[] = [
                'line' => $line,
                'coordinates' => $points,
            ];
        }

        return new ZoneDetailed($polygons);
    }

    public function arrayToPolygon(array $posted): array
    {
        $return = [];

        $line = Uuid::uuid4()->toString();
        foreach ($posted as $i => $poly) {
            $points = array_map(function ($pair) {
                return [$pair->lat, $pair->lng];
            }, $poly);
            $return[] = $this->parseSinglePolygon(
                $points,
                $i === 0 ? Polygon::TYPE_EXTERIOR : Polygon::TYPE_HOLE,
                $line
            );
        }

        return $return;
    }

    /**
     * @param array $posted
     * @return Polygon[]
     */
    private function parsePolygon(array $posted): array
    {
        $return = [];

        $line = Uuid::uuid4()->toString();
        foreach ($posted as $i => $poly) {
            $return[] = $this->parseSinglePolygon(
                $this->flipCoords($poly),
                $i === 0 ? Polygon::TYPE_EXTERIOR : Polygon::TYPE_HOLE,
                $line
            );
        }

        return $return;
    }

    private function parseSinglePolygon(array $points, int $type, string $line): Polygon
    {
        $poly = new Polygon();
        $poly->setType($type);
        $poly->setLine($line);

        // original
        $poly->setPolygon(new LineString($points));

        // simplified
        $simplified = $points;
        if (count($points) > self::CHAPAYEV_CONSTANT) {
            $simplified = $this->rdp->simplify($points, self::SIMPLIFICATION_EPSILON);
        }
        $poly->setSimplified(new LineString($simplified));

        return $poly;
    }

    private function flipCoords(array $posted)
    {
        return array_map(function (array $pair) {
            return [$pair[1], $pair[0]];
        }, $posted);
    }
}
