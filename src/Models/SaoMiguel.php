<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;

class SaoMiguel extends CarriersAbstract
{
    private $consultModel = "TRACKING_COMPLETO_POR_NOTA_FISCAL_E_COMPROVANTE";
    const ENDPOINT = "https://wsintegcli02.expressosaomiguel.com.br:40504/wsservernet/api/tracking";

    public function makeRequest(): ?Response
    {
        try {
            $client = new Client();
            $res = $client->request('POST', static::ENDPOINT, [
                "headers" => [
                    "Content-Type"      => "application/json",
                    "Customer"          => $this->order->user,
                    "Access_Key"        => $this->order->pass,
                    "Modelo_Consulta"   => $this->consultModel
                ],
                "json" => [
                    "valoresParametros" => [
                        $this->order->cnpj,     // CPF/CNPJ
                        $this->order->serial,   // Número do documento
                        1                       // Série do documento
                    ]
                ]
            ]);

            if ($res->getStatusCode() != 200) {
                throw new OrderTrackerException("Erro ao realizar requisição para o serial {$this->order->serial}", $res->getStatusCode());
            }

            $data = json_decode($res->getBody());


            if (empty($data)) {
                throw new OrderTrackerException("Sem resultado retornado pela API para o serial {$this->order->serial}");
            }

            $this->setReturn($data[0]);

            return $this->response;
        } catch (\Exception $e) {
            throw new OrderTrackerException($e->getMessage(), $e->getCode(), null, 'Sao Miguel  makeRequest()');
        }

        return null;
    }

    private function setReturn($data): void
    {
        $lastOccurrencyNumber = count($data->ocorrencias ?? 0) - 1;

        if ($lastOccurrencyNumber < 0) {
            throw new OrderTrackerException("Sem ocorrências para esse serial.");
        }

        $lastOccurrency = $data->ocorrencias[$lastOccurrencyNumber];

        $deliveryDate = null;
        $proof = null;

        if ($lastOccurrency->descricaoOcorrencia == "Entrega realizada") {
            $timezone = new DateTimeZone("-03:00");
            $deliveryDate = new DateTime($lastOccurrency->dataRegistro, $timezone);
            $proof = $lastOccurrency->idComprovante ?? null;
        }

        

        $this->response = new Response(
            $this->order->serial,
            $data->chave ?? null,
            new DateTime($data->prevEntrega) ?? null,
            $deliveryDate,
            $lastOccurrency->descricaoOcorrencia,
            is_null($proof) ? null : "Comprovante de entrega: " . $proof,
            new DateTime($lastOccurrency->dataRegistro)
        );
    }
}
