<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Locations\Locations;
use Tests\TestCase;

class LocationsTest extends TestCase
{
    /**
     * @var \LKDev\HetznerCloud\Models\Locations\Locations
     */
    protected $locations;

    public function setUp(): void
    {
        parent::setUp();
        $this->locations = new Locations($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/location.json')));
        $location = $this->locations->get(1);
        $this->assertEquals($location->id, 1);
        $this->assertEquals($location->name, 'fsn1');

        $this->assertLastRequestEquals('GET', '/locations/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/locations.json')));
        $location = $this->locations->getByName('fsn1');
        $this->assertEquals($location->id, 1);
        $this->assertEquals($location->name, 'fsn1');
        $this->assertLastRequestEquals('GET', '/locations');
        $this->assertLastRequestQueryParametersContains('name', 'fsn1');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/locations.json')));
        $locations = $this->locations->all();

        $this->assertEquals(count($locations), 1);
        $this->assertEquals($locations[0]->id, 1);
        $this->assertEquals($locations[0]->name, 'fsn1');
        $this->assertLastRequestEquals('GET', '/locations');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/locations.json')));
        $locations = $this->locations->list()->locations;

        $this->assertEquals(count($locations), 1);
        $this->assertEquals($locations[0]->id, 1);
        $this->assertEquals($locations[0]->name, 'fsn1');
        $this->assertLastRequestEquals('GET', '/locations');
    }
}
