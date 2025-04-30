<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Joaov535\OrderTracker\DTOs\Response;

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

        if ($res->getStatusCode() === 200) {
            return $body[0];
        }

        return null;
    }

    public function makeRequest(): ?Response
    {
        try {
            $this->response = new Response(111, null, null, null, null, null, null);
            $doc = $this->getDocInfo();

            if (is_null($doc) || empty($doc)) {
                $this->response = new Response($this->order->serial, null, null, null, "Não encontrado", null, null);
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
            var_dump(json_decode($res->getBody()));
            if ($res->getStatusCode() != "200") {
                return null;
            }

        } catch (\Exception $e) {
            var_dump($e);
        }
        return null;
    }
}
