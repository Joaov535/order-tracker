<?php

namespace Workers\tracking\models;

use Joaov535\OrderTracker\Models\Order;

interface CarriersInterface
{
    public function makeRequest(Order $order);
}
