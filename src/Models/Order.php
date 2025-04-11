<?php

namespace Joaov535\OrderTracker\Models;

class Order
{
    public function __construct(
        public string $serial,
        public string $cnpj,
        public ?string $token = null,
        public ?string $user = null,
        public ?string $pass = null
    ) {}
}
