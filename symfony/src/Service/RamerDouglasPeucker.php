<?php

namespace App\Service;

use InvalidArgumentException;

/**
 * SOURCE: https://rosettacode.org/wiki/Ramer-Douglas-Peucker_line_simplification#PHP
 *
 * Simplify a polygon
 */
class RamerDouglasPeucker
{
    public function simplify(array $points, $epsilon) {
        if (count($points) < 2) {
            throw new InvalidArgumentException('Not enough points to simplify');
        }

        // Find the point with the maximum distance from the line between start/end.
        $dmax = 0;
        $index = 0;
        $end = count($points) - 1;
        $start_end_line = [$points[0], $points[$end]];
        for ($i = 1; $i < $end; $i++) {
            $dist = $this->perpendicular_distance($points[$i], $start_end_line);
            if ($dist > $dmax) {
                $index = $i;
                $dmax = $dist;
            }
        }

        // If max distance is larger than epsilon, recursively simplify.
        if ($dmax > $epsilon) {
            $new_start = $this->simplify(array_slice($points, 0, $index + 1), $epsilon);
            $new_end = $this->simplify(array_slice($points, $index), $epsilon);
            array_pop($new_start);
            return array_merge($new_start, $new_end);
        }

        // Max distance is below epsilon, so return a line from with just the
        // start and end points.
        return [$points[0], $points[$end]];
    }

    private function perpendicular_distance(array $pt, array $line) {
        // Calculate the normalized delta x and y of the line.
        $dx = $line[1][0] - $line[0][0];
        $dy = $line[1][1] - $line[0][1];
        $mag = sqrt($dx * $dx + $dy * $dy);
        if ($mag > 0) {
            $dx /= $mag;
            $dy /= $mag;
        }

        // Calculate dot product, projecting onto normalized direction.
        $pvx = $pt[0] - $line[0][0];
        $pvy = $pt[1] - $line[0][1];
        $pvdot = $dx * $pvx + $dy * $pvy;

        // Scale line direction vector and subtract from pv.
        $dsx = $pvdot * $dx;
        $dsy = $pvdot * $dy;
        $ax = $pvx - $dsx;
        $ay = $pvy - $dsy;

        return sqrt($ax * $ax + $ay * $ay);
    }
}
