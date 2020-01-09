<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration\FloatingIPs;

use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

class FloatingIPsTest extends TestCase
{
    /**
     * @var FloatingIps
     */
    protected $floatingIps;

    public function setUp()
    {
        parent::setUp();
        $this->floatingIps = new FloatingIps($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $floatingIp = $this->floatingIps->get(1);
        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
    }

    public function testGetByName()
    {
        $floatingIp = $this->floatingIps->getByName('Web Frontend');
        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->name, 'Web Frontend');
    }

    public function testAll()
    {
        $floatingIps = $this->floatingIps->all();

        $this->assertEquals(count($floatingIps), 1);
        $this->assertEquals($floatingIps[0]->id, 4711);
        $this->assertEquals($floatingIps[0]->description, 'Web Frontend');
    }

    public function testList()
    {
        $floatingIps = $this->floatingIps->list()->floating_ips;

        $this->assertEquals(count($floatingIps), 1);
        $this->assertEquals($floatingIps[0]->id, 4711);
        $this->assertEquals($floatingIps[0]->description, 'Web Frontend');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreate()
    {
        $floatingIp = $this->floatingIps->create('ipv4', 'Web Frontend', new Location(123, 'nbg1', 'Falkenstein DC Park 1', 'DE', 'Falkenstein', 50.47612, 12.370071), new Server(42));

        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreateWithName()
    {
        $floatingIp = $this->floatingIps->create('ipv4', 'Web Frontend', new Location(123, 'nbg1', 'Falkenstein DC Park 1', 'DE', 'Falkenstein', 50.47612, 12.370071), new Server(42), 'WebServer');

        $this->assertEquals($floatingIp->id, 4711);
        $this->assertEquals($floatingIp->description, 'Web Frontend');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $floatingIp = $this->floatingIps->get(1);
        $this->assertTrue($floatingIp->delete());
    }
}
