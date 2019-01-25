<?php namespace App\Services;

use GuzzleHttp;

class IpStackService {

    public function fetchGeolocation($ip_address){

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

        return $responsePayload;
    }
}