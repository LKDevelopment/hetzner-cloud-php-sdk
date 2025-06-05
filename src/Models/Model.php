<?php

namespace LKDev\HetznerCloud\Models;

use GuzzleHttp\Client;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class Model
{
    /**
     * @var GuzzleClient
     */
    protected $httpClient;

    /**
     * Model constructor.
     *
     * @param  GuzzleClient  $httpClient
     */
    public function __construct(?GuzzleClient $httpClient = null)
    {
        $this->httpClient = $httpClient == null ? HetznerAPIClient::$instance->getHttpClient() : $httpClient;
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {
        return null;
    }

    /**
     * Replaces or sets the http client.
     *
     * @param  GuzzleClient  $httpClient
     */
    public function setHttpClient(?GuzzleClient $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }
}
