<?php

namespace LKDev\HetznerCloud\Models\Networks;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\Models\Servers\Server;

/**
 * Class Network.
 */
class Network extends Model implements Resource
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
     * @var string
     */
    public $ipRange;
    /**
     * @var array
     */
    public $subnets;
    /**
     * @var array
     */
    public $routes;
    /**
     * @var array
     */
    public $servers;
    /**
     * @var Protection
     */
    public $protection;
    /**
     * @var array
     */
    public $labels;
    /**
     * @var string
     */
    public $created;

    /**
     * Network constructor.
     * @param int $id
     * @param GuzzleClient|null $httpClient
     */
    public function __construct(int $id, GuzzleClient $httpClient = null)
    {
        $this->id = $id;
        parent::__construct($httpClient);
    }

    /**
     * @param Subnet $subnet
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function addSubnet(Subnet $subnet)
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/add_subnet', [
            'json' => $subnet->__toRequestPayload(),
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    /**
     * @param Subnet $subnet
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function deleteSubnet(Subnet $subnet)
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/delete_subnet', [
            'json' => ['ip_range' => $subnet->ipRange],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    /**
     * @param Route $route
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function addRoute(Route $route)
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/add_route', [
            'json' => $route->__toRequestPayload(),
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    /**
     * @param Route $route
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function deleteRoute(Route $route)
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/delete_route', [
            'json' => [$route->__toRequestPayload()],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    public function changeIPRange(string $ipRange)
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/change_ip_range', [
            'json' => ['ip_range' => $ipRange],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the protection configuration of the network.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-16
     * @param bool $delete
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): APIResponse
    {
        $response = $this->httpClient->post('networks/'.$this->id.'/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    /**
     * @param $data
     * @return $this
     */
    private function setAdditionalData($data)
    {
        $this->name = $data->name;
        $this->ipRange = $data->ip_range;
        $this->subnets = Subnet::parse($data->subnets, $this->httpClient);
        $this->routes = Route::parse($data->routes, $this->httpClient);
        $this->servers = collect($data->servers)
            ->map(function ($id) {
                return new Server($id);
            })->toArray();
        $this->protection = Protection::parse($data->protection);

        $this->labels = get_object_vars($data->labels);
        $this->created = $data->created;

        return $this;
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {
        return (new self($input->id))->setAdditionalData($input);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->networks()->get($this->id);
    }

    public function delete()
    {
        $response = $this->httpClient->delete('networks/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }
    }

    public function update(array $data)
    {
        $response = $this->httpClient->put('networks/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'network' => Server::parse(json_decode((string) $response->getBody())->network),
            ], $response->getHeaders());
        }
    }
}
