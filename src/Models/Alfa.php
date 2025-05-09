<?php

namespace Joaov535\OrderTracker\Models;

use DateTime;
use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;
use SimpleXMLElement;

class Alfa extends CarriersAbstract
{
    const ENDPOINT = "https://api.alfatransportes.com.br/rastreamento/v1.2/";

    public function makeRequest(): ?Response
    {
        try {
            $client = new Client();
            $res = $client->request(
                'POST',
                static::ENDPOINT,
                [
                    "headers" => [
                        'Accept'        => 'application/json',
                    ],
                    "json" => [
                        "cnpjTomador"   => $this->order->cnpj,
                        "idr"           => $this->order->token,
                        "merNF"         => $this->order->serial
                    ]
                ]
            );

            if ($res->getStatusCode() != "200") {
                throw new OrderTrackerException("Falha na requisição para o serial {$this->order->serial}.", $res->getStatusCode());
            }

            $xml = new SimpleXMLElement($res->getBody()); 
            // var_dump(count($xml->rst->embarque->embNF));

            if((string)$xml->rst->rstStatus != "RASTREAMENTO CONCLUIDO COM SUCESSO") {
                throw new OrderTrackerException("Erro para o serial {$this->order->serial}. ".(string)$xml->rst->rstStatus);
            }
            
            $this->setReturn($xml);

            return $this->response;
        } catch (\Exception $e) {
            throw new OrderTrackerException($e->getMessage(), $e->getCode(), null, "Braspress makeRequest()");
        }
    }

    private function setReturn(SimpleXMLElement $xml): void
    {
        $deliveryForecast = null;
        $deliveryDate = null;
        $details = null;
        $lastUpdate = null;
        $status = null;

        $deliveryForecast = new DateTime((string)$xml->rst->NF->NFDataPrevista);
        $code = (string)$xml->rst->NF->NFCtrc ?? null;

        if(isset($xml->rst->entrega) && !empty($xml->rst->entrega)) {
            $status = "Entregue";
            $deliveryDate = new DateTime((string)$xml->rst->entrega->entNF->entData . (string)$xml->rst->entrega->entNF->entHora);
            $details = "Comprovante: " . (string)$xml->rst->entrega->entNF->entComprovante;
        }else if(isset($xml->rst->embarque->embNF)) {
            $status = "Em trânsito";
            $n = count($xml->rst->embarque->embNF) - 1;
            $lastStatus = $xml->rst->embarque->embNF[$n];
            $details = "Saiu de " . $lastStatus->embOrigem . " em " . $lastStatus->embSaida;
            if(strtolower($lastStatus->embChegada) == "none") {
                $details .= " Com destino a " . $lastStatus->embDestino;
            } else {
                $details .= " e chegou a " . $lastStatus->embDestino . " em " . $lastStatus->embChegada;
            }
        }
        
        $this->response = new Response($this->order->serial, $code, $deliveryForecast, $deliveryDate, $status, $details, $lastUpdate);
    }
}
