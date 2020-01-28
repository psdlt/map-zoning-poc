<?php

namespace App\Service;

use App\Entity\Polygon;
use App\Entity\Zone;

class TurfService
{
    private const RADIUS = 6378137;

    public function zoneArea(Zone $zone)
    {
        $totalArea = 0;
        foreach ($zone->getPolygons() as $polygon) {
            $points = $polygon->getPolygon()->points;
            $area = $this->ringArea($points);

            if ($polygon->getType() === Polygon::TYPE_EXTERIOR) {
                $totalArea += $area;
            }
            if ($polygon->getType() === Polygon::TYPE_HOLE) {
                $totalArea -= $area;
            }
        }

        return $totalArea;
    }

    /**
     * @param Polygon[] $polygons
     * @return Polygon[]
     */
    public function weDontNeedNoHoles(array $polygons)
    {
        $holes = [];
        foreach ($polygons as $polygon) {
            if ($polygon->getType() === Polygon::TYPE_HOLE) {
                $holes[] = $polygon->getLine();
            }
        }

        foreach ($polygons as $index => $polygon) {
            if (in_array($polygon->getLine(), $holes)) {
                unset($polygons[$index]);
            }
        }

        return $polygons;
    }

    // https://github.com/Turfjs/turf/blob/master/packages/turf-area/index.ts
    private function ringArea($coords) {
        $total = 0;
        $coordsLength = count($coords);

        if ($coordsLength > 2) {
            for ($i = 0; $i < $coordsLength; $i++) {
                if ($i === $coordsLength - 2) { // i = N-2
                    $lowerIndex = $coordsLength - 2;
                    $middleIndex = $coordsLength - 1;
                    $upperIndex = 0;
                } else if ($i === $coordsLength - 1) { // i = N-1
                    $lowerIndex = $coordsLength - 1;
                    $middleIndex = 0;
                    $upperIndex = 1;
                } else { // i = 0 to N-3
                    $lowerIndex = $i;
                    $middleIndex = $i + 1;
                    $upperIndex = $i + 2;
                }
                $p1 = $coords[$lowerIndex];
                $p2 = $coords[$middleIndex];
                $p3 = $coords[$upperIndex];
                $total += ($this->rad($p3[1]) - $this->rad($p1[1])) * sin($this->rad($p2[0]));
            }

            $total = $total * self::RADIUS * self::RADIUS / 2;
        }

        return abs($total);
    }

    private function rad($num)
    {
        return ($num * pi()) / 180;
    }
}
