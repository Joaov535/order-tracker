<?php

namespace Joaov535\OrderTracker\Exceptions;


class OrderTrackerException extends \Exception
{
    protected string $context;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, string $context = '')
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
