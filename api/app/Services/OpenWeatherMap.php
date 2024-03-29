<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class OpenWeatherMap implements WeatherServiceInterface
{
    private string $endpoint;
    private string $appid;

    private int $rateLimit;

    private Response $response;

    private string $lon;
    private string $lat;
    public function __construct(string $lon, string $lat)
    {
        $this->endpoint = env('OPEN_WEATHER_APP_URL');
        $this->appid = env('OPEN_WEATHER_APP_KEY');
        $this->rateLimit = env('OPEN_WEATHER_APP_RATE_LIMIT');
        $this->lon = $lon;
        $this->lat = $lat;
    }
    public function weather()
    {
        $this->response = Http::withUrlParameters([
            'endpoint' => $this->endpoint,
            'appid' => $this->appid,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ])->get('{+endpoint}/weather?lat={lat}&lon={lon}&appid={appid}');
    }
    public function result() : array
    {
        $this->weather();

        if($this->response->successful()) {
            return $this->response->json();
        }else{
            return array(['error' => $this->response->status()]);
        }
    }

    public function getRateLimit() : int
    {
        return $this->rateLimit;
    }
}
