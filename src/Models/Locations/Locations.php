<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00
 */

namespace LKDev\HetznerCloud\Models\Locations;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 *
 */
class Locations extends Model implements Resources
{
    use GetFunctionTrait;
    /**
     * @var array
     */
    public $locations;

    /**
     * Returns all location objects.
     *
     * @see https://docs.hetzner.cloud/#resources-locations-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new LocationRequestOpts();
        }
        $locations = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        for ($i = 1; $i < PHP_INT_MAX; $i++) {
            $_s = $this->list($requestOpts);
            $locations = array_merge($locations, $_s);
            if (empty($_s)) {
                break;
            }
        }
        return $locations;
    }
    /**
     * Returns all location objects.
     *
     * @see https://docs.hetzner.cloud/#resources-locations-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new LocationRequestOpts();
        }
        $response = $this->httpClient->get('locations'. $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->locations;
        }
    }
    /**
     * Returns a specific location object.
     *
     * @see https://docs.hetzner.cloud/#resources-locations-get-1
     * @param int $locationId
     * @return \LKDev\HetznerCloud\Models\Locations\Location
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $locationId): Location
    {
        $response = $this->httpClient->get('locations/' . $locationId);
        if (!HetznerAPIClient::hasError($response)) {
            return Location::parse(json_decode((string)$response->getBody())->location);
        }
    }

    /**
     * Returns a specific location object by its name.
     *
     * @see https://docs.hetzner.cloud/#resources-locations-get-1
     * @param int $locationId
     * @return \LKDev\HetznerCloud\Models\Locations\Location
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): Location
    {
        $locations = $this->all(new LocationRequestOpts($name));

        return (count($locations) > 0) ? $locations[0] : null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->locations = collect($input->locations)->map(function ($location, $key) {
            return Location::parse($location);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
}
