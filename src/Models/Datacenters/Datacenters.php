<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01.
 */

namespace LKDev\HetznerCloud\Models\Datacenters;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class Datacenters extends Model implements Resources
{
    use GetFunctionTrait;
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
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new DatacenterRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    public function list(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new DatacenterRequestOpts();
        }
        $response = $this->httpClient->get('locations'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody()))->datacenters;
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
    public function getById(int $datacenterId): Datacenter
    {
        $response = $this->httpClient->get('datacenters/'.$datacenterId);
        if (! HetznerAPIClient::hasError($response)) {
            return Datacenter::parse(json_decode((string) $response->getBody())->datacenter);
        }
    }

    /**
     * Returns a specific datacenter object by its name.
     *
     * @see https://docs.hetzner.cloud/#resources-datacenters-get-1
     * @param int $datacenterId
     * @return \LKDev\HetznerCloud\Models\Datacenters\Datacenter
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): Datacenter
    {
        $datacenters = $this->list(new DatacenterRequestOpts($name));

        return (count($datacenters) > 0) ? $datacenters[0] : null;
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
