<?php

namespace App\Model;

class Pricing
{
    public $fromZone;
    public $toZone;
    public $price;

    public function __construct(
        $fromZone,
        $toZone,
        $price
    ) {
        $this->fromZone = $fromZone;
        $this->toZone = $toZone;
        $this->price = $price;
    }
}
