<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 2018-09-20
 * Time: 15:58
 */

namespace LKDev\HetznerCloud\Models\Volumes;


use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\RequestOpts;

/**
 * Class Volumes
 * @package LKDev\HetznerCloud\Models\Volumes
 */
class Volumes extends Model
{
    /**
     * @var array
     */
    public $volumes;

    /**
     * Returns all existing volume objects.
     *
     * @see https://docs.hetzner.cloud/#resources-volumes-get
     * @param RequestOpts|null $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('volumes' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->volumes;
        }
    }

    /**
     * Returns a specific server object by its name. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-volumes-get
     * @param string $volumeName
     * @return \LKDev\HetznerCloud\Models\Volumes\Volume|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $volumeName): Server
    {
        $volumes = $this->all(new VolumeRequestOpts($volumeName));

        return (count($volumes) > 0) ? $volumes[0] : null;

    }

    /**
     * Returns a specific volume object. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-volume-get-1
     * @param int $volumeId
     * @return Volume
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get(int $volumeId): Volume
    {
        $response = $this->httpClient->get('volumes/' . $volumeId);
        if (!HetznerAPIClient::hasError($response)) {
            return Volume::parse(json_decode((string)$response->getBody())->volume);
        }
    }

    /**
     * @param string $name
     * @param int $size
     * @param Server|null $server
     * @param Location|null $location
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(string $name, int $size, Server $server = null, Location $location = null): APIResponse
    {
        $payload = [
            'name' => $name,
            'size' => $size,
        ];
        if ($location == null && $server != null) {
            $payload['server'] = $server->id;
        } else if ($location != null && $server == null) {
            $payload['location'] = $location->id;
        } else {
            throw new \InvalidArgumentException("Please specify only a server or a location");
        }
        $response = $this->httpClient->post('volumes', [
            'json' => $payload,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'volume' => Volume::parse($payload->volume),
            ], $response->getHeaders());
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->volumes = collect($input->volumes)->map(function ($volume, $key) {
            if ($volume != null) {
                return Volume::parse($volume);
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
