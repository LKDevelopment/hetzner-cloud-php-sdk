<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01
 */

namespace LKDev\HetznerCloud\Models\Datacenters;

use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class Datacenter extends Model
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
     * @var \LKDev\HetznerCloud\Models\Locations\Location
     */
    public $location;

    /**
     * @var array
     */
    public $serverTypes;

    /**
     * @var bool
     */
    public $recommendation;

    /**
     * Datacenter constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param \LKDev\HetznerCloud\Models\Locations\Location $location
     * @param array $server_types
     * @param bool $recommendation
     */
    public function __construct(
        int $id,
        string $name,
        string $description,
        Location $location,
        array $server_types = null,
        bool $recommendation = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->location = $location;
        $this->serverTypes = $server_types;
        $this->recommendation = $recommendation;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\Datacenters\Datacenter|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }
       return new self($input->id,$input->name,$input->description,Location::parse($input->location),$input->server_types,$input->recommendation);
    }
}