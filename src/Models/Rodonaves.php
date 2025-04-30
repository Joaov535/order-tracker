<?php

namespace Joaov535\OrderTracker\Models;

use GuzzleHttp\Client;
use Joaov535\OrderTracker\Exceptions\OrderTrackerException;
use Joaov535\OrderTracker\DTOs\Response;


class Rodonaves extends CarriersAbstract
{
    public function makeRequest(): ?Response
    {
        $auth = $this->getAuth();
        $client = new Client();

        $response = $client->request(
            'GET',
            "https://tracking-apigateway.rte.com.br/api/v1/tracking?TaxIdRegistration={$this->order->cnpj}&InvoiceNumber={$this->order->serial}",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $auth->access_token,
                    'accept' => 'application/json',
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);

        return $this->response;
    }

    public function getAuth()
    {
        $client = new Client();

        $response = $client->request('POST', 'https://tracking-apigateway.rte.com.br/token', [
            'form_params' => [
                'auth_type' => 'DEV',
                'grant_type' => 'password',
                'username' => $this->order->user,
                'password' => $this->order->pass
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === "200") {
            return $body;
        }

        if (isset($body->error) && $body->error === "invalid_grant") {
            throw new OrderTrackerException("Credenciais inválidas.", 400, null, "Rodonaves getAuth()");
        }

        throw new OrderTrackerException("Falha ao obter token de acesso.", $response->getStatusCode(), null, "Rodonaves getAuth()");
    }
}
