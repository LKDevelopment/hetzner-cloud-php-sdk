<?php

namespace LKDev\HetznerCloud\Models;

use GuzzleHttp\Client;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class Model
{
    /**
     * @var \LKDev\HetznerCloud\Clients\GuzzleClient
     */
    protected $httpClient;

    /**
     * Model constructor.
     * @param GuzzleClient $httpClient
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
