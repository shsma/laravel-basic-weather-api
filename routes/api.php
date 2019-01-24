<?php

use Illuminate\Http\Request;

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


Route::get('/geolocation', 'ApiControllers\GeoWeatherController@getGeolocation')->name('get_geolocation');
Route::get('/geolocation/{ip_address}', 'ApiControllers\GeoWeatherController@getGeolocation')->name('get_geolocation_ip');
Route::get('/weather', 'ApiControllers\GeoWeatherController@getWeather')->name('get_weather');
Route::get('/weather/{ip_address}', 'ApiControllers\GeoWeatherController@getWeather')->name('get_weather_ip');


