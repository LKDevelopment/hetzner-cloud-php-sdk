<?php

namespace LKDev\HetznerCloud\Models;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class Model
{
    protected GuzzleClient $httpClient;

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
     */
    public function setHttpClient(?GuzzleClient $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }
}
