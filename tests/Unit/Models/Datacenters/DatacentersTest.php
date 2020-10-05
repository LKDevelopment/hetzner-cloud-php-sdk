<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\Datacenters;

use GuzzleHttp\Psr7\Response;
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
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/datacenter.json')));
        $datacenter = $this->datacenters->get(1);
        $this->assertEquals($datacenter->id, 1);
        $this->assertEquals($datacenter->name, 'fsn1-dc8');
        $this->assertLastRequestEquals('GET', '/datacenters/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/datacenters.json')));
        $datacenter = $this->datacenters->getByName('fsn1-dc8');
        $this->assertEquals($datacenter->id, 1);
        $this->assertEquals($datacenter->name, 'fsn1-dc8');
        $this->assertLastRequestQueryParametersContains('name', 'fsn1-dc8');
        $this->assertLastRequestEquals('GET', '/datacenters');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/datacenters.json')));
        $datacenters = $this->datacenters->all();

        $this->assertEquals(count($datacenters), 1);
        $this->assertEquals($datacenters[0]->id, 1);
        $this->assertEquals($datacenters[0]->name, 'fsn1-dc8');
        $this->assertLastRequestEquals('GET', '/datacenters');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/datacenters.json')));
        $datacenters = $this->datacenters->list()->datacenters;

        $this->assertEquals(count($datacenters), 1);
        $this->assertEquals($datacenters[0]->id, 1);
        $this->assertEquals($datacenters[0]->name, 'fsn1-dc8');
        $this->assertLastRequestEquals('GET', '/datacenters');
    }
}
