<?php
namespace App\Models;
use Illuminate\Support\Facades\Http;

class Weather
{
    public string $main;
    public string $description;
    public string $icon;
    public Coord $coord;
    public Temperature $temperature;
}
