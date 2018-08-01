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

    /**
     * @return Models\Servers\Servers
     */
    public function servers(){
        return new Models\Servers\Servers();
    }

    /**
     * @return Datacenters
     */
    public function datacenters(){
        return new Datacenters();
    }

    /**
     * @return Models\Locations\Locations
     */
    public function locations(){
        return new Models\Locations\Locations();
    }

    /**
     * @return Images
     */
    public function images(){
        return new Images();
    }

    /**
     * @return SSHKeys
     */
    public function ssh_keys(){
        return new SSHKeys();
    }

    /**
     * @return Prices
     */
    public function prices(){
        return new Prices();
    }

    /**
     * @return ISOs
     */
    public function isos(){
        return new ISOs();
    }

    /**
     * @return FloatingIps
     */
    public function floating_ips(){
        return new FloatingIps();
    }
}