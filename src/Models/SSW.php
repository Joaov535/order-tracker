<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use DateTimeInterface;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;

class SSW extends CarriersAbstract
{
    private const ENDPOINT = "https://ssw.inf.br/api/trackingdanfe";

    public function makeRequest(): ?Response
    {
        try {
            $client = new Client();
            $res = $client->request('POST', static::ENDPOINT, [
                "headers" => [
                    "Content-Type" => "application/json"
                ],
                "json" => [
                    "chave_nfe" => $this->order->serial
                ]
            ]);

            if ($res->getStatusCode() != "200") {
                throw new OrderTrackerException("Falha na requisição para o serial {$this->order->serial}", $res->getStatusCode());
            }

            $data = json_decode($res->getBody());

            if (!$data->success) {
                throw new OrderTrackerException("Sem resultado retornado pela API para o serial {$this->order->serial}");
            }

            $this->setReturn($data);
        } catch (\Exception $e) {
            throw new OrderTrackerException($e->getMessage(), $e->getCode(), null, 'SSW  makeRequest()');
        }

        return $this->response;
    }

    private function setReturn($data): void
    {
        if (!isset($data->documento->tracking)) {
            throw new OrderTrackerException("Sem resultado retornado pela API para o serial {$this->order->serial} no campo tracking");
        }

        $firstOccurrency = $data->documento->tracking[0];
        $deliveryForecast = null;
        $deliveryDate = null;
        $lastUpdate = null;

        if (str_contains($firstOccurrency->descricao, "Previsao de entrega:")) {
            $pos = strripos($firstOccurrency->descricao, "entrega:");
            $deliveryForecast = substr($firstOccurrency->descricao, $pos + 9, 8);
            $deliveryForecast = DateTime::createFromFormat("d/m/y", $deliveryForecast);
        }

        $occurrencyQuantity = count($data->documento->tracking);
        $index = $occurrencyQuantity > 1 ? $occurrencyQuantity - 1 : 0;
        $lastOccurrency = $data->documento->tracking[$index];

        if (str_contains(strtoupper($lastOccurrency->ocorrencia), "MERCADORIA ENTREGUE")) {
            $deliveryDate = DateTime::createFromFormat("Y-m-d\TH:i:s",  $lastOccurrency->data_hora);
        }

        $lastUpdate = DateTime::createFromFormat("Y-m-d\TH:i:s",  $lastOccurrency->data_hora);

        $this->response = new Response(
            $this->order->serial,
            $lastOccurrency->codigo_ssw,
            $deliveryForecast,
            $deliveryDate,
            $lastOccurrency->ocorrencia,
            $lastOccurrency->descricao,
            $lastUpdate
        );
    }
}
