<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:52
 */

namespace LKDev\HetznerCloud\Models\Servers;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use LKDev\HetznerCloud\RequestOpts;

/**
 *
 */
class Servers extends Model
{
    /**
     * @var array
     */
    public $servers;

    /**
     * Returns all existing server objects.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-get
     * @param RequestOpts|null $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new ServerRequestOpts();
        }
        $response = $this->httpClient->get('servers' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->servers;
        }
    }

    /**
     * Returns a specific server object. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-get-1
     * @param int $serverId
     * @return \LKDev\HetznerCloud\Models\Servers\Server
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get(int $serverId): Server
    {
        $response = $this->httpClient->get('servers/' . $serverId);
        if (!HetznerAPIClient::hasError($response)) {
            return Server::parse(json_decode((string)$response->getBody())->server);
        }
    }

    /**
     * Returns a specific server object by its name. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-get
     * @param string $serverName
     * @return \LKDev\HetznerCloud\Models\Servers\Server|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $serverName)
    {
        $servers = $this->all(new ServerRequestOpts($serverName));

        return (count($servers) > 0) ? $servers[0] : null;
    }

    /**
     * Creates a new server in a datacenter instead of in a location. Returns preliminary information about the server as well as an action that covers progress of creation
     *
     * @see https://docs.hetzner.cloud/#resources-servers-post
     * @param string $name
     * @param \LKDev\HetznerCloud\Models\Servers\Types\ServerType $serverType
     * @param \LKDev\HetznerCloud\Models\Images\Image $image
     * @param \LKDev\HetznerCloud\Models\Locations\Location $location
     * @param \LKDev\HetznerCloud\Models\Datacenters\Datacenter $datacenter
     * @param array $ssh_keys
     * @param bool $startAfterCreate
     * @param string $user_data
     * @param array $volumes
     * @param bool $automount
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function createInDatacenter(
        string $name,
        ServerType $serverType,
        Image $image,
        Datacenter $datacenter = null,
        $ssh_keys = [],
        $startAfterCreate = true,
        $user_data = '',
        $volumes = [],
        $automount = false
    ): APIResponse
    {
        $response = $this->httpClient->post('servers', [
            'json' => [
                'name' => $name,
                'server_type' => $serverType->id,
                'datacenter' => $datacenter == null ? null : $datacenter->id,
                'image' => $image->id,
                'start_after_create' => $startAfterCreate,
                'user_data' => $user_data,
                'ssh_keys' => $ssh_keys,
                'volumes' => $volumes,
                'automount' => $automount
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create(array_merge([
                'action' => Action::parse($payload->action),
                'server' => Server::parse($payload->server),
                'next_actions' => collect($payload->next_actions)->map(function ($action) {
                    return Action::parse($action);
                })->toArray(),
            ], (property_exists($payload, 'root_password')) ? ['root_password' => $payload->root_password] : []
            ), $response->getHeaders());

        }
    }

    /**
     * Creates a new server in a location instead of in a datacenter. Returns preliminary information about the server as well as an action that covers progress of creation
     *
     * @see https://docs.hetzner.cloud/#resources-servers-post
     * @param string $name
     * @param ServerType $serverType
     * @param Image $image
     * @param Location|null $location
     * @param array $ssh_keys
     * @param bool $startAfterCreate
     * @param string $user_data
     * @param array $volumes
     * @param bool $automount
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function createInLocation(string $name,
                                     ServerType $serverType,
                                     Image $image,
                                     Location $location = null,
                                     $ssh_keys = [],
                                     $startAfterCreate = true,
                                     $user_data = '',
                                     $volumes = [],
                                     $automount = false
    ): APIResponse
    {
        $response = $this->httpClient->post('servers', [
            'json' => [
                'name' => $name,
                'server_type' => $serverType->id,
                'location' => $location == null ? null : $location->id,
                'image' => $image->id,
                'start_after_create' => $startAfterCreate,
                'user_data' => $user_data,
                'ssh_keys' => $ssh_keys,
                'volumes' => $volumes,
                'automount' => $automount
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create(array_merge([
                'action' => Action::parse($payload->action),
                'server' => Server::parse($payload->server),
                'next_actions' => collect($payload->next_actions)->map(function ($action) {
                    return Action::parse($action);
                })->toArray(),
            ], (property_exists($payload, 'root_password')) ? ['root_password' => $payload->root_password] : []
            ), $response->getHeaders());

        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->servers = collect($input->servers)
            ->map(function ($server) {
                if ($server != null) {
                    return Server::parse($server);
                }
            })
            ->toArray();

        return $this;
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {

        return (new self())->setAdditionalData($input);
    }
}
