<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\Interfaces\CarriersInterface;

abstract class Carriers implements CarriersInterface
{
    protected Order $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
