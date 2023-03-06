<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use App\Services\OpenWeatherMap;
use App\Services\WeatherBuilder;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Redis;
class UserController extends Controller
{
    public function index()
    {
        $activeUsers = User::with('forecast')->active()->get();

        return response()->json([
            'data' => $activeUsers,
        ], 200);
    }

    public function show($id)
    {
        $user = User::with('forecast')->findOrFail($id);
        $key = sprintf("%s:%s", round($user->longitude, 4), round($user->latitude,4));
        $cachedForecast = Redis::get($key);

        if(!isset($cachedForecast)) {
            Artisan::call('update:weathers', ['user' => $user]);
            $cachedForecast = Redis::get($key);
        }

        $user->weather = json_decode($cachedForecast);

        return response()->json([
            'data' => $user,
        ], 200);
    }

}
