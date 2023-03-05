<?php

namespace App\Services;

interface WeatherServiceInterface
{
    public function weather();
    public function result() : array;
}
