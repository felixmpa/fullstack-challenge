<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'all systems are a go',
        'users' => \App\Models\User::all(),
    ]);
});

Route::get('user/{id}', function($id){

    Redis::set('name', 'Taylor');

    return response()->json([
        'cache' => Redis::get('name'),
        'values' => Redis::lrange('names', 5, 10),
        'user' => \App\Models\User::findOrFail($id),
    ]);
});
