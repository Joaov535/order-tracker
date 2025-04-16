<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;

class Braspress extends CarriersAbstract
{
    const ENDPOINT = "https://api.braspress.com/v3/tracking/byNf/";

    public function makeRequest(): Response
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

        $result = json_decode($res->getBody());
        $data = $result->conhecimentos[0];
        $this->response = new Response(
            $this->order->serial,
            $data->numero ?? null,
            $data->previsaoEntrega ?? null,
            $data->dataEntrega ?? null,
            $data->status ?? null,
            $data->ultimaOcorrencia ?? null,
            $data->dataOcorrencia ?? null,
        );

        return $this->response;
    }
}
