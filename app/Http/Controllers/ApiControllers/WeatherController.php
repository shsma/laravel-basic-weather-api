<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp;

class WeatherController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @param $ip
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeather(Request $request, $ip = null)
    {

        if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP))
        {
            $ip = $request->ip(); //get the left-most IP
        }

        $response = $this->fetchWeatherData($ip);
        $responsePayload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() && $response->getStatusCode() !== 200 )
        {
            return response()->json(["error" => "Unable to fetch data"], 401);

        }elseif ($responsePayload && $responsePayload->status === "fail"){
            return response()->json(["error" => $responsePayload->message], 500);
        }

        return response()->json($responsePayload,200);
    }

    private function fetchWeatherData($ip)
    {
        $baseUri = env("IP_API_URI");
        $client = new GuzzleHttp\Client();

        try {
            $res = $client->request('GET', $baseUri.$ip);

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        return $res;
    }
}
