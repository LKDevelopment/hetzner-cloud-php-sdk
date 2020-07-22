<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration;

use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use Tests\TestCase;

class DatacentersTest extends TestCase
{
    /**
     * @var Datacenters
     */
    protected $datacenters;

    public function setUp(): void
    {
        parent::setUp();
        $this->datacenters = new Datacenters($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $datacenter = $this->datacenters->get(1);
        $this->assertEquals($datacenter->id, 1);
        $this->assertEquals($datacenter->name, 'fsn1-dc8');
    }

    public function testGetByName()
    {
        $datacenter = $this->datacenters->getByName('fsn1-dc8');
        $this->assertEquals($datacenter->id, 1);
        $this->assertEquals($datacenter->name, 'fsn1-dc8');
    }

    public function testAll()
    {
        $datacenters = $this->datacenters->all();

        $this->assertEquals(count($datacenters), 1);
        $this->assertEquals($datacenters[0]->id, 1);
        $this->assertEquals($datacenters[0]->name, 'fsn1-dc8');
    }

    public function testList()
    {
        $datacenters = $this->datacenters->list()->datacenters;

        $this->assertEquals(count($datacenters), 1);
        $this->assertEquals($datacenters[0]->id, 1);
        $this->assertEquals($datacenters[0]->name, 'fsn1-dc8');
    }
}
