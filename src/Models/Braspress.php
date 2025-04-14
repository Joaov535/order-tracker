<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;

class Braspress extends Carriers
{
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";

    public function makeRequest()
    {
        $token = $this->order->token ?? base64_encode($this->order->user . ":" . $this->order->pass);

        $client = new Client();
        $res = $client->request(
            'GET',
            static::ENDPOINT . $this->order->cnpj . "/" . $this->order->serial . "/json",
            [
                "headers" => [
                    "Authorization" => "Basic " . $token,
                    'Accept' => 'application/json',
                ]
            ]
        );

        // var_dump($res->getHeader());
    }
}
