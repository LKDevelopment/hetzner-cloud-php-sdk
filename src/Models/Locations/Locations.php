<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00
 */

namespace LKDev\HetznerCloud\Models\Locations;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class Locations extends Model
{
    /**
     * @var array
     */
    public $locations;

    /**
     * Returns all location objects.
     *
     * @see https://docs.hetzner.cloud/#resources-locations-get
     * @param string|null $name
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(string $name = null): array
    {
        $response = $this->httpClient->get('locations'. (($name != null) ? '?name=' . $name : ''));
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
    public function get(int $locationId): Location
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
        $locations = $this->all($name);

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
