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
     * Datacenter constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param \LKDev\HetznerCloud\Models\Locations\Location $location
     * @param array $server_types
     */
    public function __construct(
        int $id,
        string $name,
        string $description,
        Location $location,
        $server_types = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->location = $location;
        $this->serverTypes = $server_types;
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
       return new self($input->id,$input->name,$input->description,Location::parse($input->location), $input->server_types);
    }
}