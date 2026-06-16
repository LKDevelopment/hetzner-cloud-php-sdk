<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
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
    public function all(?RequestOpts $requestOpts = null): array
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
    public function list(?RequestOpts $requestOpts = null): ?APIResponse
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
     * Creates a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancers-create-a-load-balancer
     *
     * @param  string  $name
     * @param  string  $loadBalancerType  ID or name of the Load Balancer type
     * @param  string|null  $location  ID or name of location (mutually exclusive with $networkZone)
     * @param  string|null  $networkZone  Name of network zone (mutually exclusive with $location)
     * @param  array|null  $algorithm
     * @param  array  $labels
     * @param  int|null  $network
     * @param  bool  $publicInterface
     * @param  array  $services
     * @param  array  $targets
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(string $name, string $loadBalancerType, ?string $location = null, ?string $networkZone = null, ?array $algorithm = null, array $labels = [], ?int $network = null, bool $publicInterface = true, array $services = [], array $targets = []): ?APIResponse
    {
        $payload = [
            'name' => $name,
            'load_balancer_type' => $loadBalancerType,
        ];
        if ($location !== null) {
            $payload['location'] = $location;
        }
        if ($networkZone !== null) {
            $payload['network_zone'] = $networkZone;
        }
        if ($algorithm !== null) {
            $payload['algorithm'] = $algorithm;
        }
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }
        if ($network !== null) {
            $payload['network'] = $network;
        }
        if (! $publicInterface) {
            $payload['public_interface'] = false;
        }
        if (! empty($services)) {
            $payload['services'] = $services;
        }
        if (! empty($targets)) {
            $payload['targets'] = $targets;
        }

        $response = $this->httpClient->post('load_balancers', ['json' => $payload]);
        if (! HetznerAPIClient::hasError($response)) {
            $body = json_decode((string) $response->getBody());

            return APIResponse::create([
                'load_balancer' => LoadBalancer::parse($body->load_balancer),
                'action' => Action::parse($body->action),
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
        $this->load_balancers = array_map(function ($loadBalancer) {
            return LoadBalancer::parse($loadBalancer);
        }, $input);

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
