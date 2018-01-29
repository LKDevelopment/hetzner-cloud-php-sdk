<?php

namespace LKDev\HetznerCloud\Clients;

use GuzzleHttp\Client;

class GuzzleClient extends Client
{
    /**
     *
     * @param string $apiToken
     */
    public function __construct(\LKDev\HetznerCloud\HetznerAPIClient $client)
    {
        parent::__construct([
            'base_uri' => $client->getBaseUrl(),
            'headers' => ['Authorization' => 'Bearer '.$client->getApiToken(), 'Content-Type' => 'application/json'],
        ]);
    }
}