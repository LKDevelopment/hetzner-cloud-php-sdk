<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 2018-09-20
 * Time: 15:58.
 */

namespace LKDev\HetznerCloud\Models\Volumes;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 * Class Volumes.
 */
class Volumes extends Model implements Resources
{
    use GetFunctionTrait;

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

        return $this->_all($requestOpts);
    }

    /**
     * Returns all existing volume objects.
     *
     * @see https://docs.hetzner.cloud/#resources-volumes-get
     * @param RequestOpts|null $requestOpts
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new VolumeRequestOpts();
        }
        $response = $this->httpClient->get('volumes'.$requestOpts->buildQuery());
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
     * Returns a specific server object by its name. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-volumes-get
     * @param string $volumeName
     * @return \LKDev\HetznerCloud\Models\Volumes\Volume|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $volumeName): ?Volume
    {
        $volumes = $this->list(new VolumeRequestOpts($volumeName));

        return (count($volumes->volumes) > 0) ? $volumes->volumes[0] : null;
    }

    /**
     * Returns a specific volume object. The server must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-volume-get-1
     * @param int $id
     * @return Volume|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id): ?Volume
    {
        $response = $this->httpClient->get('volumes/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return Volume::parse(json_decode((string) $response->getBody())->volume);
        }

        return null;
    }

    /**
     * @param string $name
     * @param int $size
     * @param Server|null $server
     * @param Location|null $location
     * @param bool $automount
     * @param string|null $format
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(string $name, int $size, Server $server = null, Location $location = null, bool $automount = false, string $format = null): ?APIResponse
    {
        $payload = [
            'name' => $name,
            'size' => $size,
            'automount' => $automount,
        ];
        if ($location == null && $server != null) {
            $payload['server'] = $server->id;
        } elseif ($location != null && $server == null) {
            $payload['location'] = $location->id;
        } else {
            throw new \InvalidArgumentException('Please specify only a server or a location');
        }
        if ($format != null) {
            $payload['format'] = $format;
        }
        $response = $this->httpClient->post('volumes', [
            'json' => $payload,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'volume' => Volume::parse($payload->volume),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->volumes = collect($input)->map(function ($volume, $key) {
            if ($volume != null) {
                return Volume::parse($volume);
            }

            return null;
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

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'volume', 'many' => 'volumes'];
    }
}
