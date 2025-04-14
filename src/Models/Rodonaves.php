<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\Interfaces\CarriersInterface;


class Rodonaves extends CarriersInterface
{
    private Order $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function makeRequest() {
        $client = new Guzzle
    }
}
