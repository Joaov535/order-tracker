<?php

namespace Joaov535\OrderTracker\Interfaces;

use Joaov535\OrderTracker\Models\Order;

interface CarriersInterface
{
    public function __construct(Order $order);
    public function makeRequest();
}
