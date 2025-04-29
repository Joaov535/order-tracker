<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;

class Braspress extends CarriersAbstract
{
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";

    public function makeRequest(): ?Response
    {
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
            return null;
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
            $this->response = new Response(
                $this->order->serial,
                null,
                null,
                null,
                null,
                $result->message ?: null,
                null,
                true
            );
        } else {
            $this->response = new Response(
                $this->order->serial,
                null,
                null,
                null,
                null,
                null,
                null,
                false
            );
        }
        return $this->response;
    }
}
