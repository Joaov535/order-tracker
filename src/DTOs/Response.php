<?php

namespace Joaov535\OrderTracker\DTOs;

use DateTimeInterface;

class Response
{
    public function __construct(
        protected int $serial,
        protected ?string $carrierCode,
        protected ?DateTimeInterface $deliveryForecast,
        protected ?DateTimeInterface $deliveryDate,
        protected ?string $status,
        protected ?string $details,
        protected ?DateTimeInterface $lastUpdate,
        protected bool $errors = false
    ) {}

    public function getSerial(): int
    {
        return $this->serial;
    }

    public function getCarrierCode(): ?string
    {
        return $this->carrierCode;
    }

    public function getDeliveryForecast(): ?DateTimeInterface
    {
        return $this->deliveryForecast;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getLastUpdate(): ?DateTimeInterface
    {
        return $this->lastUpdate;
    }
}
