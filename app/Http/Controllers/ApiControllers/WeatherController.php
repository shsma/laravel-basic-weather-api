<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp;
use App\Services\IpApiService;
use App\Services\IpStackService;


class WeatherController extends Controller
{

    const CELSIUS = 273;

    private $ipApiService;
    private $ipStackService;

    public function __construct(IpApiService $ipApiService, IpStackService $ipStackService )
    {
        //init geolocation services
        $this->ipApiService = $ipApiService;
        $this->ipStackService = $ipStackService;
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

        $client = new GuzzleHttp\Client();

        //call ipApiService
        $geoPayload = $this->ipApiService->fetchGeolocation($ip_address);

        if (!isset($geoPayload->city))
        {
            //give it a try with ipStack
            $geoPayload = $this->ipStackService->fetchGeolocation($ip_address);

            if (!isset($geoPayload->city))
            {
                return response()->json(["error" => "Unable to fetch city name"], 401);
            }
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
                "current" => number_format($responsePayload->main->temp - self::CELSIUS, 2),
                "low"     => number_format($responsePayload->main->temp_min -self::CELSIUS, 2),
                "high"    => number_format($responsePayload->main->temp_max - self::CELSIUS, 2),
            ],
            "wind"=>[
                "speed"     => $responsePayload->wind->speed,
                "direction" => $responsePayload->wind->deg,
            ]
        ];

        return response()->json($data, 200);
    }
}
