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
    public function getGeolocation(Request $request, $ip = null)
    {

        if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP))
        {
            $ip = $request->ip(); //get the left-most IP
        }

        if($request->get("service") && trim($request->get("service")) === env("IP_STACK_SERVICE") ){

            $response = $this->fetchFromIpStack($ip);

        }else{

            //default geo service ip-api
            $response = $this->fetchFromIpApi($ip);
        }

        return $response;
    }

    private function fetchFromIpApi($ip)
    {
        $baseUri = env("IP_API_URI");
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUri.$ip);

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
            "ip" => $ip,
            "geo"=>[
                "service" => env("IP_API_SERVICE"),
                "city"    => $responsePayload->city,
                "region"  => $responsePayload->region,
                "country" => $responsePayload->country
            ]
        ];

        return response()->json($data, 200);
    }

    private function fetchFromIpStack($ip)
    {
        $baseUri = env("IP_STACK_URI");
        $accessKey = env("IP_STACK_ACCESS_KEY");
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUri.$ip."?access_key=".$accessKey);

        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            return response()->json(["error" => $e->getMessage()], 500);
        }

        //handle Api response error
        if($response->getStatusCode() && $response->getStatusCode() !== 200 ) {
            return response()->json(["error" => "Unable to fetch data from ".env("IP_STACK_SERVICE")], 401);
        }

        $responsePayload = json_decode($response->getBody()->getContents());

        $data = [
            "ip" => $ip,
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
