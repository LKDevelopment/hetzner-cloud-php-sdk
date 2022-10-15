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
     * @var LoadBalancerAlgorithm
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
     * @param  int  $id
     * @param  string  $name
     * @param  LoadBalancerAlgorithm  $algorithm
     * @param  string  $created
     * @param  int  $included_traffic
     * @param  array  $labels
     * @param  LoadBalancerType  $loadBalancerType
     * @param  Location  $location
     * @param  array  $private_net
     * @param  array|Protection  $protection
     * @param  array  $public_net
     * @param  array  $services
     * @param  array  $targets
     * @param  int|null  $ingoing_traffic
     * @param  int|null  $outgoing_traffic
     */
    public function __construct(int $id, string $name, LoadBalancerAlgorithm $algorithm, string $created, int $included_traffic, array $labels, LoadBalancerType $loadBalancerType, Location $location, array $private_net, $protection, $public_net, array $services, array $targets, int $ingoing_traffic = null, int $outgoing_traffic = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->algorithm = $algorithm;
        $this->created = $created;
        $this->included_traffic = $included_traffic;
        $this->labels = $labels;
        $this->loadBalancerType = $loadBalancerType;
        $this->location = $location;
        $this->private_net = $private_net;
        $this->protection = $protection;
        $this->public_net = $public_net;
        $this->services = $services;
        $this->targets = $targets;
        $this->ingoing_traffic = $ingoing_traffic;
        $this->outgoing_traffic = $outgoing_traffic;
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

        return new self($input->id, $input->name, LoadBalancerAlgorithm::parse($input->algorithm), $input->created, $input->included_traffic, get_object_vars($input->labels), LoadBalancerType::parse($input->load_balancer_type), Location::parse($input->location), $input->private_net, Protection::parse($input->protection), $input->public_net, $input->services, $input->targets, $input->ingoing_traffic, $input->outgoing_traffic);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->loadBalancers()->get($this->id);
    }

    public function delete()
    {
        $response = $this->httpClient->delete('load_balancers/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    public function update(array $data)
    {
        $response = $this->httpClient->put('load_balancers/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->load_balancer);
        }

        return null;
    }

    /**
     * @param  string  $uri
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
     * @param  string  $destinationPort
     * @param  LoadBalancerHealthCheck  $healthCheck
     * @param  int  $listenPort
     * @param  string  $protocol
     * @param  string  $proxyprotocol
     * @param  LoadBalancerServiceHttp|null  $http
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function addService(string $destinationPort, LoadBalancerHealthCheck $healthCheck, int $listenPort, string $protocol, string $proxyprotocol, LoadBalancerServiceHttp $http = null): ?APIResponse
    {
        $payload = [
            'destination_port' => $destinationPort,
            'health_check' => $healthCheck,
            'listen_port' => $listenPort,
            'protocol' => $protocol,
            'proxyprotocol' => $proxyprotocol,
        ];
        if ($http != null) {
            $payload['http'] = $http;
        }
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/add_service'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Adds a target to a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-add-target
     *
     * @param  string  $type
     * @param  LoadBalancerTargetIp|null  $ip
     * @param  bool  $usePrivateIp
     * @param  array  $labelSelector
     * @param  Server|null  $server
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function addTarget(string $type, LoadBalancerTargetIp $ip = null, bool $usePrivateIp = false, array $labelSelector = [], Server $server = null): ?APIResponse
    {
        $payload = [
            'type' => $type,
            'use_private_ip' => $usePrivateIp,
        ];
        if ($ip != null) {
            $payload['ip'] = $ip;
        }
        if (! empty($labelSelector)) {
            $payload['label_selector'] = $labelSelector;
        }
        if ($server != null) {
            $payload['server'] = $server;
        }
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/add_target'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Attach a Load Balancer to a Network.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-attach-a-load-balancer-to-a-network
     *
     * @param  int  $network
     * @param  string  $ip
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function attachLoadBalancerToNetwork(int $network, string $ip = ''): ?APIResponse
    {
        $payload = [
            'network' => $network,
        ];
        if (! empty($ip)) {
            $payload['ip'] = $ip;
        }

        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/attach_to_network'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Change the algorithm that determines to which target new requests are sent.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-change-algorithm
     *
     * @param  string  $type
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function changeAlgorithm(string $type): ?APIResponse
    {
        $payload = [
            'type' => $type,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/change_algorithm'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the hostname that will appear when getting the hostname belonging to the public IPs (IPv4 and IPv6) of this Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-change-reverse-dns-entry-for-this-load-balancer
     *
     * @param  string  $dnsPtr
     * @param  string  $ip
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function changeReverseDnsEntry(string $dnsPtr, string $ip): ?APIResponse
    {
        $payload = [
            'dns_ptr' => $dnsPtr,
            'ip' => $ip,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/change_dns_ptr'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the protection configuration of a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-change-load-balancer-protection
     *
     * @param  bool  $delete
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function changeProtection(bool $delete = false): ?APIResponse
    {
        $payload = [
            'delete' => $delete,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/change_protection'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the type (Max Services, Max Targets and Max Connections) of a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-change-the-type-of-a-load-balancer
     *
     * @param  string  $loadBalancerType
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function changeType(string $loadBalancerType): ?APIResponse
    {
        $payload = [
            'load_balancer_type' => $loadBalancerType,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/change_type'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Delete a service of a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-delete-service
     *
     * @param  int  $listenPort
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function deleteService(int $listenPort): ?APIResponse
    {
        $payload = [
            'listen_port' => $listenPort,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/delete_service'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Detaches a Load Balancer from a network.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-detach-a-load-balancer-from-a-network
     *
     * @param  int  $network
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function detachFromNetwork(int $network): ?APIResponse
    {
        $payload = [
            'network' => $network,
        ];
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/detach_from_network'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Disable the public interface of a Load Balancer. The Load Balancer will be not accessible from the internet via its public IPs.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-disable-the-public-interface-of-a-load-balancer
     *
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function disablePublicInterface(): ?APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/disable_public_interface'), []);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Enable the public interface of a Load Balancer. The Load Balancer will be accessible from the internet via its public IPs.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-enable-the-public-interface-of-a-load-balancer
     *
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function enablePublicInterface(): ?APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/enable_public_interface'), []);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Removes a target from a Load Balancer.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-remove-target
     *
     * @param  string  $type
     * @param  LoadBalancerTargetIp|null  $ip
     * @param  array|null  $labelSelector
     * @param  Server|null  $server
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function removeTarget(string $type, LoadBalancerTargetIp $ip = null, array $labelSelector = null, Server $server = null): ?APIResponse
    {
        $payload = [
            'type' => $type,
        ];
        if ($ip != null) {
            $payload['ip'] = $ip;
        }
        if (! empty($labelSelector)) {
            $payload['label_selector'] = $labelSelector;
        }
        if ($server != null) {
            $payload['server'] = $server;
        }
        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/remove_target'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Updates a Load Balancer Service.
     *
     * @see https://docs.hetzner.cloud/#load-balancer-actions-update-service
     *
     * @param  int  $destinationPort
     * @param  LoadBalancerHealthCheck  $healthCheck
     * @param  int  $listenPort
     * @param  string  $protocol
     * @param  bool  $proxyprotocol
     * @param  LoadBalancerServiceHttp|null  $http
     * @return APIResponse|null
     *
     * @throws APIException
     * @throws GuzzleException
     */
    public function updateService(int $destinationPort, LoadBalancerHealthCheck $healthCheck, int $listenPort, string $protocol, bool $proxyprotocol, LoadBalancerServiceHttp $http = null): ?APIResponse
    {
        $payload = [
            'destination_port' => $destinationPort,
            'health_check' => $healthCheck,
            'listen_port' => $listenPort,
            'protocol' => $protocol,
            'proxyprotocol' => $proxyprotocol,
        ];
        if ($http != null) {
            $payload['http'] = $http;
        }

        $response = $this->httpClient->post($this->replaceServerIdInUri('load_balancers/{id}/actions/update_service'), [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }
}
