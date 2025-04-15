<?php

namespace Joaov535\OrderTracker\Interfaces;

use Joaov535\OrderTracker\Models\Order;
use Joaov535\OrderTracker\DTOs\Response;

interface CarriersInterface
{
    public function __construct(Order $order);
    public function makeRequest(): Response;
}
