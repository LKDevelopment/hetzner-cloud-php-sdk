<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:58
 */

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 *
 */
class ServerTypes extends Model implements Resources
{
    use GetFunctionTrait;
    /**
     * @var array
     */
    public $serverTypes;

    /**
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $server_types = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        for ($i = 1; $i < PHP_INT_MAX; $i++) {
            $_s = $this->list($requestOpts);
            $ssh_keys = array_merge($server_types, $_s);
            if (empty($_s)) {
                break;
            }
        }
        return $server_types;
    }

    /**
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('server_types' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->serverTypes;
        }
    }

    /**
     * @param int $serverTypeId
     * @return \LKDev\HetznerCloud\Models\Servers\Types\ServerType
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $serverTypeId)
    {
        $response = $this->httpClient->get('server_types/' . $serverTypeId);
        if (!HetznerAPIClient::hasError($response)) {
            return ServerType::parse(json_decode((string)$response->getBody())->server_type);
        }
    }

    /**
     * Returns a specific server type object by its name.
     *
     * @param int $serverTypeId
     * @return \LKDev\HetznerCloud\Models\Servers\Types\ServerType
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name)
    {
        $serverTypes = $this->all(new ServerTypesRequestOpts($name));

        return (count($serverTypes) > 0) ? $serverTypes[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->serverTypes = collect($input->server_types)->map(function ($serverType, $key) {
            return ServerType::parse($serverType);
        })->toArray();

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
}
