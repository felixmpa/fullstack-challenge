<?php

namespace App\Services;
class WeatherBuilder
{
    private WeatherServiceInterface $weatherService;
    public function __construct(WeatherServiceInterface $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    public function weather() : array
    {
        return $this->weatherService->result();
    }
}
