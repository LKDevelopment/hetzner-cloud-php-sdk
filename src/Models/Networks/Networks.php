<?php

namespace LKDev\HetznerCloud\Models\Networks;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 * Class Networks.
 */
class Networks extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $networks;

    /**
     * Returns all existing server objects.
     *
     * @see https://docs.hetzner.cloud/#networks-get-all-networks
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(?RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new NetworkRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all existing server objects.
     *
     * @see https://docs.hetzner.cloud/#networks-get-all-networks
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(?RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new NetworkRequestOpts();
        }
        $response = $this->httpClient->get('networks'.$requestOpts->buildQuery());
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
     * Returns a specific server object. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#networks-get-a-network
     *
     * @param  int  $serverId
     * @return Network
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $serverId): ?Network
    {
        $response = $this->httpClient->get('networks/'.$serverId);
        if (! HetznerAPIClient::hasError($response)) {
            return Network::parse(json_decode((string) $response->getBody())->network);
        }

        return null;
    }

    /**
     * Returns a specific network object by its name. The network must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#networks-get-all-networks
     *
     * @param  string  $name
     * @return Network|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?Network
    {
        $networks = $this->list(new NetworkRequestOpts($name));

        return (count($networks->networks) > 0) ? $networks->networks[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->networks = array_map(function ($network) {
            if ($network != null) {
                return Network::parse($network);
            }
        }, $input);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $ipRange
     * @param  array  $subnets
     * @param  array  $routes
     * @param  array  $labels
     * @param  bool  $exposeRoutesToVswitch
     */
    public function create(string $name, string $ipRange, array $subnets = [], array $routes = [], array $labels = [], bool $exposeRoutesToVswitch = false)
    {
        $payload = [
            'name' => $name,
            'ip_range' => $ipRange,
            'expose_routes_to_vswitch' => $exposeRoutesToVswitch,
        ];
        if (! empty($subnets)) {
            $payload['subnets'] = array_map(function (Subnet $s) {
                return $s->__toRequestPayload();
            }, $subnets);
        }
        if (! empty($routes)) {
            $payload['routes'] = array_map(function (Route $r) {
                return $r->__toRequestPayload();
            }, $routes);
        }
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }

        $response = $this->httpClient->post('networks', [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'network' => Network::parse($payload->network),
            ], $response->getHeaders());
        }
    }

    /**
     * @param  $input
     * @return static
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
        return ['one' => 'network', 'many' => 'networks'];
    }
}
