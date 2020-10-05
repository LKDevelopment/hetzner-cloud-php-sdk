<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\ISO;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\ISOs\ISOs;
use Tests\TestCase;

class ISOsTest extends TestCase
{
    /**
     * @var ISOs
     */
    protected $isos;

    public function setUp(): void
    {
        parent::setUp();
        $this->isos = new ISOs($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/iso.json')));

        $iso = $this->isos->get(4711);
        $this->assertEquals($iso->id, 4711);
        $this->assertEquals($iso->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');

        $this->assertLastRequestEquals('GET', '/isos/4711');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/isos.json')));

        $isos = $this->isos->all();

        $this->assertEquals(count($isos), 1);
        $this->assertEquals($isos[0]->id, 4711);
        $this->assertEquals($isos[0]->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');

        $this->assertLastRequestEquals('GET', '/isos');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/isos.json')));
        $isos = $this->isos->list()->isos;

        $this->assertEquals(count($isos), 1);
        $this->assertEquals($isos[0]->id, 4711);
        $this->assertEquals($isos[0]->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');

        $this->assertLastRequestEquals('GET', '/isos');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/isos.json')));
        $iso = $this->isos->getByName('FreeBSD-11.0-RELEASE-amd64-dvd1');
        $this->assertEquals($iso->id, 4711);
        $this->assertEquals($iso->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');
        $this->assertLastRequestQueryParametersContains('name', 'FreeBSD-11.0-RELEASE-amd64-dvd1');
        $this->assertLastRequestEquals('GET', '/isos');
    }
}
