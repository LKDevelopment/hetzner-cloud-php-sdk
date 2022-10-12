<?php

namespace LKDev\HetznerCloud\Models\LoadBalancerTypes;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class LoadBalancerTypes extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $loadBalancerTypes;

    /**
     * Returns all load balancer type objects.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-types-get-all-load-balancer-types
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new LoadBalancerTypeRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all load balancer type objects.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-types-get-all-load-balancer-types
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new LoadBalancerTypeRequestOpts();
        }
        $response = $this->httpClient->get('load_balancer_types'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Gets a specific Load Balancer type object.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-types-get-a-load-balancer-type
     *
     * @param  int  $id
     * @return \LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id): ?LoadBalancerType
    {
        $response = $this->httpClient->get('load_balancer_types/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return LoadBalancerType::parse(json_decode((string) $response->getBody())->load_balancer_type);
        }

        return null;
    }

    /**
     * Gets a specific Load Balancer type object by its name.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-types-get-a-load-balancer-type
     *
     * @param  string  $name
     * @return \LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?LoadBalancerType
    {
        $loadBalancerTypes = $this->list(new LoadBalancerTypeRequestOpts($name));

        return (count($loadBalancerTypes->load_balancer_type) > 0) ? $loadBalancerTypes->load_balancer_type[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->loadBalancerTypes = collect($input)->map(function ($loadBalancerType, $key) {
            return LoadBalancerType::parse($loadBalancerType);
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
        return ['one' => 'load_balancer_type', 'many' => 'load_balancer_types'];
    }
}
