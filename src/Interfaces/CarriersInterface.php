<?php

namespace Workers\tracking\models;

use Joaov535\OrderTracker\Models\Order;

interface CarriersInterface
{
    public function __construct(Order $order);
    public function makeRequest();
}
