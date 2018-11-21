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
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\Models\Servers\Server;

/**
 * Class Volume
 * @package LKDev\HetznerCloud\Models\Volumes
 */
class Volume extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $size;

    /**
     * @var Server
     */
    public $server;

    /**
     * @var Location
     */
    public $location;
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
    public $linux_device;

    /**
     * @param $data
     * @return Volume
     */
    public function setAdditionalData($data)
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->linux_device = $data->linux_device;
        $this->size = $data->size;
        try {
            $this->server = $data->server != null ? HetznerAPIClient::$instance->servers()->get($data->server) : null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->server = null;
        }
        $this->location = Location::parse($data->location);
        $this->protection = $data->protection ?: Protection::parse($data->protection);
        $this->labels = $data->labels;

        return $this;
    }

    /**
     * Deletes a volume. This immediately removes the volume from your account, and it is no longer accessible.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-delete
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): APIResponse
    {
        $response = $this->httpClient->delete('volumes/' . $this->id);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([], $response->getHeaders());
        }
    }

    /**
     * @param Server $server
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function attach(Server $server)
    {
        $response = $this->httpClient->post('volumes/' . $this->id . '/actions/attach', [
            'json' => [
                'server' => $server->id,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function detach()
    {
        $response = $this->httpClient->post('volumes/' . $this->id . '/actions/detach');
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * @param int $size
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function resize(int $size)
    {
        $response = $this->httpClient->post('volumes/' . $this->id . '/actions/resize', [
            'json' => [
                'size' => $size,
            ]
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Update a volume with new meta data.
     *
     * @see https://docs.hetzner.cloud/#resources-volume-put
     * @param array $data
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data)
    {
        $response = $this->httpClient->put('volumes/' . $this->id, [
            'json' => [
                $data
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'volume' => Volume::parse(json_decode((string)$response->getBody())->volume)
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the protection configuration of the volume.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ip-actions-post-3
     * @param bool $delete
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): APIResponse
    {
        $response = $this->httpClient->post('volumes/' . $this->id . '/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * @param  $input
     * @return Volume|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return (new self())->setAdditionalData($input);
    }

}