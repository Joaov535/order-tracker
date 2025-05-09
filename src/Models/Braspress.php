<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;

class Braspress extends CarriersAbstract
{
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";

    public function makeRequest(): ?Response
    {
        try {
            $token = $this->order->token ?? base64_encode($this->order->user . ":" . $this->order->pass);

            $client = new Client();
            $res = $client->request(
                'GET',
                static::ENDPOINT . $this->order->cnpj . "/" . $this->order->serial . "/json",
                [
                    "headers" => [
                        "Authorization" => "Basic " . $token,
                        'Accept' => 'application/json',
                    ]
                ]
            );

            if ($res->getStatusCode() != "200") {
                throw new OrderTrackerException("Erro ao realizar requisição para o serial {$this->order->serial}", $res->getStatusCode());
            }

            $result = json_decode($res->getBody());
            if (isset($result->conhecimentos[0])) {
                $data = $result->conhecimentos[0];
                $this->response = new Response(
                    $this->order->serial,
                    $data->numero ?? null,
                    DateTime::createFromFormat("d/m/Y", $data->previsaoEntrega) ?: null,
                    DateTime::createFromFormat("d/m/Y", $data->dataEntrega) ?: null,
                    $data->status ?? null,
                    $data->ultimaOcorrencia ?? null,
                    DateTime::createFromFormat("d/m/Y H:i:s", $data->dataOcorrencia) ?: null,
                );
            } else if (isset($result->statusCode)) {
                throw new OrderTrackerException("Erro. Serial {$this->order->serial}. " . $result->message);
            } else {
                throw new OrderTrackerException("Sem resultado retornado pela API para o serial {$this->order->serial}");
            }
            return $this->response;
        } catch (\Exception $e) {
            throw new OrderTrackerException($e->getMessage(), $e->getCode(), null, "Braspress makeRequest()");
        }
    }
}
