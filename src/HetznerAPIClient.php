<?php

namespace LKDev\HetznerCloud;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
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
     * @var \LKDev\HetznerCloud\HetznerAPIClient
     */
    public static $hetznerApiClient;

    /**
     * @var \LKDev\HetznerCloud\Clients\GuzzleClient
     */
    public static $httpClient;

    /**
     *
     * @param string $apiToken
     * @param string $baseUrl
     */
    public function __construct(string $apiToken, $baseUrl = 'https://api.hetzner.cloud/v1/')
    {
        $this->apiToken = $apiToken;
        $this->baseUrl = $baseUrl;
        self::$hetznerApiClient = $this;
        self::$httpClient = new GuzzleClient($this);
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

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws \LKDev\HetznerCloud\APIException
     */
    public static function throwError(ResponseInterface $response)
    {
        var_dump(json_decode((string) $response->getBody()));
        die();
        // throw new APIException($response, ->error->code);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public static function hasError(ResponseInterface $response)
    {
        if ((property_exists($response, 'error')) || ($response->getStatusCode() <= 200 && $response->getStatusCode() >= 300)) {
            self::throwError($response);

            return true;
        }

        return false;
    }
}