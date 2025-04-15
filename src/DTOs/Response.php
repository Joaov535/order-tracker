<?php

namespace Joaov535\OrderTracker\DTOs;

use DateTime;
use DateTimeInterface;

class Response
{
    protected int $serial;
    protected string $carrierCode;
    protected DateTimeInterface $deliveryForecast;
    protected DateTimeInterface $deliveryDate;
    protected string $status;
    protected string $details;
    protected DateTimeInterface $lastUpdate;

    public function __construct($serial, $carrierCode, $deliveryForecast, $deliveryDate, $status, $details, $lastUpdate)
    {
        $this->serial = $serial;
        $this->carrierCode = $carrierCode;
        $this->deliveryForecast = new DateTime($deliveryForecast);
        $this->deliveryDate = new DateTime($deliveryDate);
        $this->status = $status;
        $this->details = $details;
        $this->lastUpdate = new DateTime($lastUpdate);
    }

    public function getSerial(): int
    {
        return $this->serial;
    }

    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    public function getDeliveryForecast(): DateTimeInterface
    {
        return $this->deliveryForecast;
    }

    public function getDeliveryDate(): DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getLastUpdate(): DateTimeInterface
    {
        return $this->lastUpdate;
    }
}
