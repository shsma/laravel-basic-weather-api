<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IpApiService;
use App\Services\IpStackService;

class GeolocationController extends Controller
{
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
    public function getGeolocation(Request $request, $ip_address = null)
    {

        if(!$ip_address || !filter_var($ip_address, FILTER_VALIDATE_IP))
        {
            $ip_address = $request->ip(); //get the left-most IP
        }

        if($request->get("service") && trim($request->get("service")) === env("IP_STACK_SERVICE") ){

            $response = $this->processIpStackService($ip_address);

        }else{

            //default geo service ip-api
            $response = $this->processIpApiService($ip_address);
        }

        return $response;
    }

    private function processIpApiService($ip_address)
    {
        $response = $this->ipApiService->fetchGeolocation($ip_address);

        if (!isset($response->city) || !isset($response->region) || !isset($response->country))
        {
            return response()->json(["error" => "Unable to fetch data from ". env("IP_API_SERVICE")], 500);
        }

        $data = [
            "ip" => $ip_address,
            "geo"=>[
                "service" => env("IP_API_SERVICE"),
                "city"    => $response->city,
                "region"  => $response->region,
                "country" => $response->country
            ]
        ];

        return response()->json($data, 200);
    }

    private function processIpStackService($ip_address)
    {


        $response = $this->ipStackService->fetchGeolocation($ip_address);

        if (!isset($response->city) || !isset($response->region_name) || !isset($response->country_name))
        {
            return response()->json(["error" => "Unable to fetch data from ". env("IP_STACK_SERVICE")], 500);
        }

        $data = [
            "ip" => $ip_address,
            "geo"=>[
                "service" => env("IP_STACK_SERVICE"),
                "city"    => $response->city,
                "region"  => $response->region_name,
                "country" => $response->country_name
            ]
        ];

        return response()->json($data, 200);
    }
}
