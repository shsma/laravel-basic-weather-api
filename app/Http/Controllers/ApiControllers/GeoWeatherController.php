<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp;

class GeoWeatherController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @param $ip_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeolocation(Request $request, $ip_address = null)
    {

        if(!$ip_address || !filter_var($ip_address, FILTER_VALIDATE_IP))
        {
            $ip_address = $request->ip(); //get the left-most IP
        }

        if($request->get("service") && trim($request->get("service")) === env("IP_STACK_SERVICE") ){

            $response = $this->fetchFromIpStack($ip_address);

        }else{

            //default geo service ip-api
            $response = $this->fetchFromIpApi($ip_address);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param $ip_address
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeather(Request $request, $ip_address = null)
    {

        if(!$ip_address || !filter_var($ip_address, FILTER_VALIDATE_IP))
        {
            $ip_address = $request->ip(); //get the left-most IP
        }

        $response = $this->fetchCurrentWeather($ip_address);

        return $response;
    }

    private function fetchCurrentWeather($ip_address)
    {
        $baseUri = env("OPEN_WEATHER_URI");
        $apiKey = env("OPEN_WEATHER_KEY");
        $geoBaseUri = env("IP_API_URI");
        $client = new GuzzleHttp\Client();

        try {
            $geoCoordinate = $client->request('GET', $geoBaseUri.$ip_address);

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        $geoPayload = json_decode($geoCoordinate->getBody()->getContents());


        //handle Api response error
        if($geoCoordinate->getStatusCode() && $geoCoordinate->getStatusCode() !== 200 )
        {
            return response()->json(["error" => "Unable to fetch data from ". env("IP_API_SERVICE")], 401);

        }elseif ($geoPayload && $geoPayload->status === "fail"){
            return response()->json(["error" => $geoPayload->message], 500);
        }


        try {
            $response = $client->request('GET', $baseUri."q=$geoPayload->city&appid=$apiKey");

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        $responsePayload = json_decode($response->getBody()->getContents());

        if(isset($responsePayload->error)) {
            return response()->json(["error" => $responsePayload->error]);
        }

        $data = [
            "ip" => $ip_address,
            "city" => $geoPayload->city,
            "temperature"=>[
                "current" => number_format($responsePayload->main->temp -273, 2),
                "low"     => number_format($responsePayload->main->temp_min -273, 2),
                "high"    => number_format($responsePayload->main->temp_max - 273, 2),
            ],
            "wind"=>[
                "speed"        => $responsePayload->wind->speed,
                "direction"    => $responsePayload->wind->deg,
            ]
        ];

        return response()->json($data, 200);
    }

    private function fetchFromIpApi($ip_address)
    {
        $baseUri = env("IP_API_URI");
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUri.$ip_address);

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        $responsePayload = json_decode($response->getBody()->getContents());


        //handle Api response error
        if($response->getStatusCode() && $response->getStatusCode() !== 200 )
        {
            return response()->json(["error" => "Unable to fetch data from ". env("IP_API_SERVICE")], 401);

        }elseif ($responsePayload && $responsePayload->status === "fail"){
            return response()->json(["error" => $responsePayload->message], 500);
        }

        $data = [
            "ip" => $ip_address,
            "geo"=>[
                "service" => env("IP_API_SERVICE"),
                "city"    => $responsePayload->city,
                "region"  => $responsePayload->region,
                "country" => $responsePayload->country
            ]
        ];

        return response()->json($data, 200);
    }

    private function fetchFromIpStack($ip_address)
    {
        $baseUri = env("IP_STACK_URI");
        $accessKey = env("IP_STACK_ACCESS_KEY");
        $client = new GuzzleHttp\Client();

        try {

            $response = $client->request('GET', $baseUri.$ip_address."?access_key=".$accessKey);

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        //handle Api response error
        if($response->getStatusCode() && $response->getStatusCode() !== 200 ) {
            return response()->json(["error" => "Unable to fetch data from ".env("IP_STACK_SERVICE")], 401);
        }

        $responsePayload = json_decode($response->getBody()->getContents());

        $data = [
            "ip" => $ip_address,
            "geo"=>[
                "service" => env("IP_STACK_SERVICE"),
                "city"    => $responsePayload->city,
                "region"  => $responsePayload->region_name,
                "country" => $responsePayload->country_name
            ]
        ];

        return response()->json($data, 200);
    }
}
