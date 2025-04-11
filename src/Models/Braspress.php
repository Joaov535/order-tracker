<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Workers\tracking\models\CarriersInterface;

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
        $client = new Client();
        $res = $client->request(
            'GET',
            static::ENDPOINT . $this->order->cnpj . "/" . $this->order->serial . "/json",
            [
                "Authorization: Bearer " . $this->order->token
            ]
        );
    }
}
