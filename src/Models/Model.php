<?php

namespace LKDev\HetznerCloud\Models;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;

/**
 *
 */
abstract class Model
{
    /**
     * @var \LKDev\HetznerCloud\HetznerAPIClient
     */
    protected $hetznerAPIClient;

    /**
     * @var \LKDev\HetznerCloud\Clients\GuzzleClient
     */
    protected $httpClient;

    /**
     * Model constructor.
     *
     * @param \LKDev\HetznerCloud\HetznerAPIClient $hetznerAPIClient
     * @param GuzzleClient
     */
    public function __construct()
    {
        $this->hetznerAPIClient = HetznerAPIClient::$hetznerApiClient;
        $this->httpClient = HetznerAPIClient::$httpClient;
    }

    /**
     * @param $input
     * @return static
     */
    public static function parse($input)
    {
    }
}