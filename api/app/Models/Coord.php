<?php

namespace App\Models;

class Coord
{
    public string $lon;
    public string $lat;
    public function __construct(string $lon, string $lat)
    {
        $this->lon = $lon;
        $this->lat = $lat;
    }
}
