<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\DTOs\Response;

class Tnt extends CarriersAbstract
{
    const ENDPOINT = "";

    public function makeRequest(): ?Response
    {
        return $this->response;
    }
}
