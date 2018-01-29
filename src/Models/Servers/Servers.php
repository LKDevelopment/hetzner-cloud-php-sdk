<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:52
 */

namespace LKDev\HetznerCloud\Models\Servers;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;

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
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(): array
    {
        $response = $this->httpClient->get('servers');
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody()))->servers;
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
        $response = $this->httpClient->get('servers/'.$serverId);
        if (! HetznerAPIClient::hasError($response)) {
            return Server::parse(json_decode((string) $response->getBody())->server);
        }
    }

    /**
     * Creates a new server. Returns preliminary information about the server as well as an action that covers progress of creation
     *
     * @see https://docs.hetzner.cloud/#resources-servers-post
     * @param string $name
     * @param \LKDev\HetznerCloud\Models\Servers\Types\ServerType $serverType
     * @param \LKDev\HetznerCloud\Models\Datacenters\Datacenter $datacenter
     * @param \LKDev\HetznerCloud\Models\Locations\Location $location
     * @param \LKDev\HetznerCloud\Models\Images\Image $image
     * @param bool $startAfterCreate
     * @param string $user_data
     * @param array $ssh_keys
     * @return \LKDev\HetznerCloud\Models\Servers\Server
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $name,
        ServerType $serverType,
        Image $image,
        Location $location = null,
        Datacenter $datacenter = null,
        $ssh_keys = [],
        $startAfterCreate = true,
        $user_data = ''
    ): Server {
        $response = $this->httpClient->post('servers', [
            'json' => [
                'name' => $name,
                'server_type' => $serverType->id,
                'datacenter' => $datacenter == null ? null : $datacenter->id,
                'location' => $location == null ? null : $location->id,
                'image' => $image->id,
                'start_after_create' => $startAfterCreate,
                'user_data' => $user_data,
                'ssh_keys' => $ssh_keys,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return Server::parse(json_decode((string) $response->getBody()));
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->servers = collect($input->servers)->map(function ($server, $key) {
            if ($server != null) {
                return Server::parse($server);
            }
        })->toArray();

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