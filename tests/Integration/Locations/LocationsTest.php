<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31
 */

namespace Tests\Integration;

use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use LKDev\HetznerCloud\Models\Locations\Locations;
use Tests\TestCase;

/**
 *
 */
class LocationsTest extends TestCase
{
    /**
     * @var \LKDev\HetznerCloud\Models\Locations\Locations
     */
    protected $locations;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->locations = new Locations($this->hetznerApi->getHttpClient());
    }

    /**
     *
     */
    public function testGet()
    {
        $location = $this->locations->get(1);
        $this->assertEquals($location->id, 1);
        $this->assertEquals($location->name, 'fsn1');
    }
    /**
     *
     */
    public function testGetByName()
    {
        $location = $this->locations->getByName('fsn1');
        $this->assertEquals($location->id, 1);
        $this->assertEquals($location->name, 'fsn1');
    }

    /**
     *
     */
    public function testAll()
    {
        $locations = $this->locations->all();

        $this->assertEquals(count($locations), 1);
        $this->assertEquals($locations[0]->id, 1);
        $this->assertEquals($locations[0]->name, 'fsn1');
    }
}
