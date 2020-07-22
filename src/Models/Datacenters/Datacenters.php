<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01.
 */

namespace LKDev\HetznerCloud\Models\Datacenters;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 * Class Datacenters.
 */
class Datacenters extends Model implements Resources
{
    use GetFunctionTrait;
    /**
     * @var array
     */
    protected $datacenters;

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

    /**
     * List datacenter objects.
     *
     * @see https://docs.hetzner.cloud/#resources-datacenters-get
     * @param RequestOpts|null $requestOpts
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new DatacenterRequestOpts();
        }
        $response = $this->httpClient->get('datacenters'.$requestOpts->buildQuery());

        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
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
            return Datacenter::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
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
    public function getByName(string $name): ?Datacenter
    {
        $resp = $this->list(new DatacenterRequestOpts($name));

        return (count($resp->datacenters) > 0) ? $resp->datacenters[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->datacenters = collect($input)->map(function ($datacenter, $key) {
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

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'datacenter', 'many' => 'datacenters'];
    }
}
