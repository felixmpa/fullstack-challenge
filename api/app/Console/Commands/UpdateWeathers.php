<?php

namespace App\Console\Commands;

use App\Models\Temperature;
use App\Models\User;
use App\Models\Weather;
use Faker\Calculator\Ean;
use Illuminate\Console\Command;
use App\Models\Coord;
use App\Services\WeatherBuilder;
use App\Services\OpenWeatherMap;
use Redis;
use Illuminate\Support\Facades;

class UpdateWeathers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:weathers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weather for all registered coords';

    /**
     * Execute the console command.
     *
     * Get distinct latitude, longitude of active's users.
     */
    public function handle(): void
    {
        $activeUsers = User::active()->distinct('latitude','longitude');

        $attempt = 0;

        foreach($activeUsers->get() as $user)
        {
            $service = new OpenWeatherMap($user->latitude, $user->longitude);
            $weatherBuilder = new WeatherBuilder($service);

            //rate limit based on seconds
            if($attempt >= $service->getRateLimit())
                sleep(5);

            //keep extract data using generic compatibility with another weather services integration
            if($service instanceof OpenWeatherMap); {
                $data = $weatherBuilder->weather();
                $weather = $this->extractOpenWeatherMapData($data);
            }

            if(property_exists($weather, 'coord') &&
                property_exists($weather, 'temperature')) {
                Redis::set(sprintf("%s:%s", $weather->coord->lat, $weather->coord->lon), json_encode($weather));
            }

            $attempt++;
        }

        $this->info("update:weathers completed - $attempt coords processed");
    }

    private function extractOpenWeatherMapData($data): Weather
    {
        if(array_key_exists('coord', $data))
        {
            $objCoord = json_decode(json_encode($data['coord']), false);
            $objWeather = json_decode(json_encode($data['weather'][0]), false);
            $objTemp = json_decode(json_encode($data['main']), false);

            $weather = new Weather();
            $weather->main = $objWeather->main;
            $weather->description = $objWeather->description;
            $weather->icon = $objWeather->icon;
            $weather->coord = new Coord($objCoord->lat, $objCoord->lon);
            $weather->temperature = new Temperature($objTemp);

            return $weather;
        }else{
            return new Weather();
        }
    }


}
