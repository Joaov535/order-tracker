<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\Interfaces\CarriersInterface;
use Joaov535\OrderTracker\DTOs\Response;

abstract class CarriersAbstract implements CarriersInterface
{
    protected Order $order;
    protected Response $response;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
