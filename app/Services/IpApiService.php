<?php namespace App\Services;

use GuzzleHttp;

class IpApiService {

    public function fetchGeolocation($ip_address)
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

        return $responsePayload;
    }

}