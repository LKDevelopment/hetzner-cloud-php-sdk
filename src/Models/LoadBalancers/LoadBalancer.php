<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use GuzzleHttp\Exception\GuzzleException;
use LKDev\HetznerCloud\APIException;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\Models\Servers\Server;

class LoadBalancer extends Model implements Resource
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $algorithm;

    /**
     * @var string
     */
    public $created;

    /**
     * @var int
     */
    public $included_traffic;

    /**
     * @var int
     */
    public $ingoing_traffic;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var LoadBalancerType
     */
    public $loadBalancerType;

    /**
     * @var Location
     */
    public $location;

    /**
     * @var int
     */
    public $outgoing_traffic;

    /**
     * @var array
     */
    public $private_net;

    /**
     * @var array|Protection
     */
    public $protection;

    /**
     * @var array
     */
    public $public_net;

    /**
     * @var array
     */
    public $services;

    /**
     * @var array
     */
    public $targets;

    /**
     * @param int $id
     * @param string $name
     * @param array $algorithm
     * @param string $created
     * @param int $included_traffic
     * @param int $ingoing_traffic
     * @param array $labels
     * @param LoadBalancerType $loadBalancerType
     * @param Location $location
     * @param int $outgoing_traffic
     * @param array $private_net
     * @param array|Protection $protection
     * @param array $public_net
     * @param array $services
     * @param array $targets
     */
    public function __construct(int $id, string $name, array $algorithm, string $created, int $included_traffic, int $ingoing_traffic, array $labels, LoadBalancerType $loadBalancerType, Location $location, int $outgoing_traffic, array $private_net, $protection, array $public_net, array $services, array $targets)
    {
        $this->id = $id;
        $this->name = $name;
        $this->algorithm = $algorithm;
        $this->created = $created;
        $this->included_traffic = $included_traffic;
        $this->ingoing_traffic = $ingoing_traffic;
        $this->labels = $labels;
        $this->loadBalancerType = $loadBalancerType;
        $this->location = $location;
        $this->outgoing_traffic = $outgoing_traffic;
        $this->private_net = $private_net;
        $this->protection = $protection;
        $this->public_net = $public_net;
        $this->services = $services;
        $this->targets = $targets;
        parent::__construct();
    }


    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancer|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->id, $input->name, $input->algorithm, $input->created, $input->included_traffic, $input->ingoing_traffic, get_object_vars($input->labels), LoadBalancerType::parse($input->loadBalancerType), Location::parse($input->location), $input->outgoing_traffic, $input->private_net, Protection::parse($input->protection), $input->public_net, $input->services, $input->targets);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->loadBalancers()->get($this->id);
    }

    public function delete()
    {
        $response = $this->httpClient->delete('load_balancers/' . $this->id);
        if (!HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    public function update(array $data)
    {
        $response = $this->httpClient->put('load_balancers/' . $this->id, [
            'json' => $data,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody())->load_balancer);
        }

        return null;
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function replaceServerIdInUri(string $uri): string
    {
        return str_replace('{id}', $this->id, $uri);
    }

    /**
     * Adds a service to a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-add-service
     *
     * @param string $destinationPort
     * @param array $healthCheck
     * @param int $listenPort
     * @param string $protocol
     * @param string $proxyprotocol
     * @param array $http
     * @return APIResponse|null
     *
     * @throws APIException
     */
    public function addService(string $destinationPort, array $healthCheck, int $listenPort, string $protocol, string $proxyprotocol, array $http = []): ?APIResponse
    {
        $payload = [
            'destination_port' => $destinationPort,
            'health_check' => $healthCheck,
            'listen_port' => $listenPort,
            'protocol' => $protocol,
            'proxyprotocol' => $proxyprotocol,
        ];
        if (!empty($http)) {
            $payload['http'] = $http;
        }
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/add_service'), [
            'json' => $payload
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Adds a target to a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-add-target
     *
     * @param string $ip
     * @param string $type
     * @param bool $usePrivateIp
     * @param array $labelSelector
     * @param Server|null $server
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function addTarget(string $ip, string $type, bool $usePrivateIp = false, array $labelSelector = [], Server $server = null): ?APIResponse
    {
        $payload = [
            'ip' => $ip,
            'type' => $type,
            'use_private_ip' => $usePrivateIp
        ];
        if (!empty($labelSelector)) {
            $payload['label_selector'] = $labelSelector;
        }
        if ($server != null) {
            $payload['server'] = $server;
        }
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/add_target'), [
            'json' => $payload
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }
}
