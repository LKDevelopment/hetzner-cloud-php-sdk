<?php

namespace LKDev\HetznerCloud\Models;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;

/**
 *
 */
class Model
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
    public function __construct(HetznerAPIClient $hetznerAPIClient, $httpClient = null)
    {
        $this->hetznerAPIClient = $hetznerAPIClient;
        $this->httpClient = ($httpClient) ?: new GuzzleClient();
    }
}