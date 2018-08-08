<?php

namespace LKDev\HetznerCloud;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\Actions\Actions;
use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Images\Images;
use LKDev\HetznerCloud\Models\ISOs\ISOs;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class HetznerAPIClient
{
    /**
     *
     */
    const VERSION = "1.0.0";
    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var string
     */
    protected $userAgent;
    /**
     * @var HetznerAPIClient
     */
    public static $instance;
    /**
     * @var \LKDev\HetznerCloud\Clients\GuzzleClient
     */
    protected $httpClient;


    /**
     *
     * @param string $apiToken
     * @param string $baseUrl
     * @param string $userAgent
     */
    public function __construct(string $apiToken, $baseUrl = 'https://api.hetzner.cloud/v1/', $userAgent = '')
    {
        $this->apiToken = $apiToken;
        $this->baseUrl = $baseUrl;
        $this->userAgent = $userAgent;
        $this->httpClient = new GuzzleClient($this);
        self::$instance = $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
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
     * @return GuzzleClient
     */
    public function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws \LKDev\HetznerCloud\APIException
     */
    public static function throwError(ResponseInterface $response)
    {
        var_dump(json_decode((string)$response->getBody()));
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

    /**
     * @return Models\Servers\Servers
     */
    public function servers()
    {
        return new Models\Servers\Servers($this->httpClient);
    }

    /**
     * @return Datacenters
     */
    public function datacenters()
    {
        return new Datacenters($this->httpClient);
    }

    /**
     * @return Models\Locations\Locations
     */
    public function locations()
    {
        return new Models\Locations\Locations($this->httpClient);
    }

    /**
     * @return Images
     */
    public function images()
    {
        return new Images($this->httpClient);
    }

    /**
     * @return SSHKeys
     */
    public function ssh_keys()
    {
        return new SSHKeys($this->httpClient);
    }

    /**
     * @return Prices
     */
    public function prices()
    {
        return new Prices($this->httpClient);
    }

    /**
     * @return ISOs
     */
    public function isos()
    {
        return new ISOs($this->httpClient);
    }

    /**
     * @return FloatingIps
     */
    public function floating_ips()
    {
        return new FloatingIps($this->httpClient);
    }
}