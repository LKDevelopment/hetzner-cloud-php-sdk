<?php

namespace LKDev\HetznerCloud;

class HetznerAPIClient
{
    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @var string
     */
    protected $baseUrl;
    /**
     *
     * @param string $apiToken
     * @param string $baseUrl
     */
    public function __construct(string $apiToken, $baseUrl = 'https://api.hetzner.cloud/v1/')
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

}