<?php

namespace LKDev\Tests\Unit\Models\PrimaryIps;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\PrimaryIps\PrimaryIps;
use LKDev\Tests\TestCase;

class PrimaryIPsTest extends TestCase
{
    /**
     * @var PrimaryIps
     */
    protected $primaryIps;

    public function setUp(): void
    {
        parent::setUp();
        $this->primaryIps = new PrimaryIps($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP.json')));
        $primaryIP = $this->primaryIps->get(1);
        $this->assertEquals($primaryIP->id, 4711);
        $this->assertEquals($primaryIP->name, 'my-resource');
        $this->assertLastRequestEquals('GET', '/primary_ips/1');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIPs.json')));
        $primaryIP = $this->primaryIps->getByName('my-resource');
        $this->assertEquals($primaryIP->id, 4711);
        $this->assertEquals($primaryIP->name, 'my-resource');

        $this->assertLastRequestQueryParametersContains('name', 'my-resource');
        $this->assertLastRequestEquals('GET', '/primary_ips');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIPs.json')));
        $primaryIPs = $this->primaryIps->all();

        $this->assertEquals(count($primaryIPs), 1);
        $this->assertEquals($primaryIPs[0]->id, 4711);
        $this->assertEquals($primaryIPs[0]->name, 'my-resource');
        $this->assertLastRequestEquals('GET', '/primary_ips');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIPs.json')));
        $primaryIPs = $this->primaryIps->list()->primary_ips;

        $this->assertEquals(count($primaryIPs), 1);
        $this->assertEquals($primaryIPs[0]->id, 4711);
        $this->assertEquals($primaryIPs[0]->name, 'my-resource');
        $this->assertLastRequestEquals('GET', '/primary_ips');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testCreateWithName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP.json')));
        $primaryIp = $this->primaryIps->create(
            'ipv4', 'Web Frontend', 'server'
        );

        $this->assertEquals($primaryIp->id, 4711);
        $this->assertEquals($primaryIp->name, 'my-resource');
        $this->assertLastRequestEquals('POST', '/primary_ips');
        $this->assertLastRequestBodyParametersEqual(['type' => 'ipv4', 'name' => 'Web Frontend', 'assignee_type' => 'server']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP.json')));
        $floatingIp = $this->primaryIps->get(4711);
        $this->assertLastRequestEquals('GET', '/primary_ips/4711');

        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($floatingIp->delete());
        $this->assertLastRequestEquals('DELETE', '/primary_ips/4711');
    }
}
