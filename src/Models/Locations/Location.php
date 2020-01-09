<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00.
 */

namespace LKDev\HetznerCloud\Models\Locations;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class Location extends Model implements Resource
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
    public $description;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

    /**
     * @var float
     */
    public $latitude;

    /**
     * @var float
     */
    public $longitude;

    /**
     * @var string
     */
    public $networkZone;

    /**
     * Location constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $country
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @param string $networkZone
     */
    public function __construct(
        int $id,
        string $name,
        string $description = null,
        string $country = null,
        string $city = null,
        float $latitude = null,
        float $longitude = null,
        string $networkZone = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->country = $country;
        $this->city = $city;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->networkZone = $networkZone;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\Locations\Location|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }
        $networkZone = property_exists($input, 'network_zone') ? $input->network_zone : null;

        return new self($input->id, $input->name, $input->description, $input->country, $input->city, $input->latitude, $input->longitude, $networkZone);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->locations()->get($this->id);
    }

    public function delete()
    {
        throw new \BadMethodCallException('delete on location is not possible');
    }

    public function update(array $data)
    {
        throw new \BadMethodCallException('update on location is not possible');
    }
}
