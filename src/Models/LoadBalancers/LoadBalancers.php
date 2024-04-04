<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class LoadBalancers extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $load_balancers;

    /**
     * Gets all existing Load Balancers that you have available.
     *
     * @see https://docs.hetzner.cloud/#load-balancers-get-all-load-balancers
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new LoadBalancerRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Gets all existing Load Balancers that you have available.
     *
     * @see https://docs.hetzner.cloud/#load-balancers-get-all-load-balancers
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new LoadBalancerRequestOpts();
        }
        $response = $this->httpClient->get('load_balancers'.$requestOpts->buildQuery());
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
     * Gets a specific Load Balancer object.
     *
     * @see https://docs.hetzner.cloud/#load-balancers-get-a-load-balancer
     *
     * @param  int  $id
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancer
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id): ?LoadBalancer
    {
        $response = $this->httpClient->get('load_balancers/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return LoadBalancer::parse(json_decode((string) $response->getBody())->load_balancer);
        }

        return null;
    }

    /**
     * Gets a specific Load Balancer object.
     *
     * @see https://docs.hetzner.cloud/#load-balancers-get-a-load-balancer
     *
     * @param  string  $name
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancer
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?LoadBalancer
    {
        $loadBalancers = $this->list(new LoadBalancerRequestOpts($name));

        return (count($loadBalancers->load_balancers) > 0) ? $loadBalancers->load_balancers[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->load_balancers = collect($input)->map(function ($loadBalancer, $key) {
            return LoadBalancer::parse($loadBalancer);
        })->toArray();

        return $this;
    }

    /**
     * @param  $input
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
        return ['one' => 'load_balancer', 'many' => 'load_balancers'];
    }
}
