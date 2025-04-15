<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\Interfaces\CarriersInterface;

abstract class CarriersAbstract implements CarriersInterface
{
    protected Order $order;
    protected Response $response;
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->response = new Response();
    }
}
