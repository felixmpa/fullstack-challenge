<?php

namespace App\Console\Commands;

use App\Models\Temperature;
use App\Models\User;
use App\Models\Weather;
use App\Models\UserWeather;
use Illuminate\Console\Command;
use App\Models\Coord;
use App\Services\WeatherBuilder;
use App\Services\OpenWeatherMap;
use Redis;
use DB;

class UpdateWeathers extends Command
{
    protected $attempts;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:weathers {user?}';

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
        $user = $this->argument('user');

        if ($user != null) {

            $this->cacheUserForecast($user); //for single unusual call from controller

        } else {

            $activeUsers = User::active()->distinct('latitude', 'longitude');
            $this->attempts = 0;

            foreach ($activeUsers->get() as $user) {
                $this->attempts++;
                $this->cacheUserForecast($user);
            }

            $this->info("update:weathers completed - $this->attempts coords processed");
        }
    }

    public function cacheUserForecast($user)
    {
        $service = new OpenWeatherMap($user->longitude, $user->latitude);
        $weatherBuilder = new WeatherBuilder($service);

        //rate limit based on seconds
        if($this->attempts >= $service->getRateLimit())
            sleep(5);

        //keep extract data using generic compatibility with another weather services integration
        if($service instanceof OpenWeatherMap); {
            $data = $weatherBuilder->weather();
            $weather = $this->extractOpenWeatherMapData($data);
        }

        if(property_exists($weather, 'coord') && property_exists($weather, 'temperature')) {
            DB::table('users_weathers')->updateOrInsert(
                ['user_id' => $user->id],
                ['temp' => $weather->temperature->temp]
            );
            $key = sprintf("%s:%s", $weather->coord->lon, $weather->coord->lat);
            Redis::set($key, json_encode($weather));
        }
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
            $weather->coord = new Coord($objCoord->lon, $objCoord->lat);
            $weather->temperature = new Temperature($objTemp);

            return $weather;
        }else{
            return new Weather();
        }
    }


}
