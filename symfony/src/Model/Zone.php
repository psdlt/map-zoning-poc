<?php

namespace App\Model;

class Zone
{
    public $uuid;
    public $geoJson;
    public $name;

    public function __construct($uuid, $geoJson, $name)
    {
        $this->uuid = $uuid;
        $this->geoJson = $geoJson;
        $this->name = $name;
    }
}
