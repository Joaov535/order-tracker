<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;

class Tnt extends CarriersAbstract
{
    const ENDPOINT_INFO_DOC = "https://radar.tntbrasil.com.br/radargateway/public/localizacaoSimplificada/search";
    const ENDPOINT_HISTORIC = "https://radar.tntbrasil.com.br/radargateway/public/localizacaoSimplificadaDetail/detail/";

    public function getDocInfo(): ?Object
    {
        $client = new Client();

        $res = $client->request('POST', static::ENDPOINT_INFO_DOC, [
            'json' => [
                "nrDocumento" => $this->order->serial,
                "nrIdentificacao" => $this->order->cnpj,
                "remDest" => "R",
                "tpDocumento" => "NF"
            ],
            'headers' => [
                'content-type' => '	application/json',
            ],
        ]);

        $body = json_decode($res->getBody());

        if ($res->getStatusCode() !== 200 || empty($body)) {
            return null;
        }

        return $body[0];
    }

    public function makeRequest(): ?Response
    {
        try {
            $this->response = new Response(111, null, null, null, null, null, null);
            $doc = $this->getDocInfo();

            if (is_null($doc) || empty($doc)) {
                $this->response = new Response($this->order->serial, null, null, null, "Sem resposta", null, null);
                return $this->response;
            }

            $client = new Client();
            $res = $client->request(
                'GET',
                static::ENDPOINT_HISTORIC . trim($doc->id),
                [
                    "headers" => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            $result = json_decode($res->getBody());

            if ($res->getStatusCode() != "200") {
                throw new OrderTrackerException("Falha na requisição para o serial {$this->order->serial}", $res->getStatusCode());
            }

            if(empty($result->trackingHistory)) {
                throw new OrderTrackerException("Sem resultado retornado pela API para o serial {$this->order->serial}");
            }

            $this->setReturn($result);

            return $this->response;
        } catch (\Exception $e) {
            throw new OrderTrackerException($e->getMessage(), $e->getCode(), null, 'TNT makeRequest()');
        }

        return null;
    }

    private function setReturn($data): void
    {
        $history = $data->trackingHistory[0];
        $lastEvent = isset($data->eventoAtualInfo) ? $data->eventoAtualInfo : null;
        $deliveryDate = null;
        $details = null;
        $lastUpdate = new DateTime();

        if($history->occurrence == "Entrega realizada")
        {
            $deliveryDate = DateTime::createFromFormat('d/m/Y H:i', $history->timeDate);
            $details = $history->observations;
        } else {
            $details = $history->occurrence . " " . $history-> timeDate . " " . $history->observations;

            if($history->occurrence != "Previsão de chegada") {
                $lastUpdate = DateTime::createFromFormat('d/m/Y H:i', $history->timeDate);
            }
        }

        $this->response = new Response($this->order->serial, $data->idDocumento ?? null, DateTime::createFromFormat('d/m/Y', $lastEvent->previsaoEntrega), $deliveryDate, $history->occurrence, $details, $lastUpdate);
    }
}
