<?php

namespace Joaov535\OrderTracker\Models;

use Joaov535\OrderTracker\DTOs\Response;

class SSW extends CarriersAbstract
{

    public function makeRequest(): Response
    {
        return $this->response;
    }
}
