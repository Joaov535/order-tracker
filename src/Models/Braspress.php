<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Joaov535\OrderTracker\Interfaces\CarriersInterface;

class Braspress implements CarriersInterface
{
    private Order $order;
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

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

        var_dump($res);
    }
}
