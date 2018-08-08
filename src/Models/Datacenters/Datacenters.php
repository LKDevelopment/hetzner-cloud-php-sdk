<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01
 */

namespace LKDev\HetznerCloud\Models\Datacenters;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

class Datacenters extends Model
{
    /**
     * @var array
     */
    public $datacenters;

    /**
     * Returns all datacenter objects.
     *
     * @see https://docs.hetzner.cloud/#resources-datacenters-get
     * @param string $name
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(string $name = null): array
    {
        $response = $this->httpClient->get('datacenters' . (($name != null) ? '?name=' . $name : ''));
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->datacenters;
        }
    }

    /**
     * Returns a specific datacenter object.
     *
     * @see https://docs.hetzner.cloud/#resources-datacenters-get-1
     * @param int $datacenterId
     * @return \LKDev\HetznerCloud\Models\Datacenters\Datacenter
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get(int $datacenterId): Datacenter
    {
        $response = $this->httpClient->get('datacenters/' . $datacenterId);
        if (!HetznerAPIClient::hasError($response)) {
            return Datacenter::parse(json_decode((string)$response->getBody())->datacenter);
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->datacenters = collect($input->datacenters)->map(function ($datacenter, $key) {
            return Datacenter::parse($datacenter);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
}