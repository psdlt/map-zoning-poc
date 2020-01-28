<?php

namespace App\Model;

class LineString
{
    public $points = [];

    public function __construct(array $points)
    {
        $this->points = $points;
    }
}
