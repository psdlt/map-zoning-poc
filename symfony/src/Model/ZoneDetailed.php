<?php

namespace App\Model;

class ZoneDetailed
{
    public $polygons;

    public function __construct(array $polygons)
    {
        $this->polygons = $polygons;
    }
}
