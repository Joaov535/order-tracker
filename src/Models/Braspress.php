<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Workers\tracking\models\CarriersInterface;

class Braspress implements CarriersInterface
{
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";
    public function __construct() {}

    public function makeRequest(Order $order)
    {
        $client = new Client();
        $res = $client->request(
            'GET',
            static::ENDPOINT . $order->cnpj . "/" . $order->serial . "/json",
            [
                "Authorization: Bearer " . $order->token
            ]
        );
    }
}
