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
        $this->deliveryForecast = DateTime::createFromFormat('d/m/Y', $deliveryForecast);
        $this->deliveryDate = DateTime::createFromFormat('d/m/Y', $deliveryDate);
        $this->status = $status;
        $this->details = $details;
        $this->lastUpdate = DateTime::createFromFormat('d/m/Y H:i:s', $lastUpdate);
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
