<?php

namespace LKDev\HetznerCloud\Models;

use GuzzleHttp\Client;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class Model
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Model constructor.
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient = null)
    {
        $this->httpClient = $httpClient == null ? HetznerAPIClient::$instance->getHttpClient() : $httpClient;
    }

    /**
     * @param $input
     * @return static
     */
    public static function parse($input)
    {
    }
}
