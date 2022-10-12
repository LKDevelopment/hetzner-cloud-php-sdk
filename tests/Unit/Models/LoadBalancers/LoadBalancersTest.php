<?php

namespace LKDev\Tests\Unit\Models\LoadBalancers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancers;
use LKDev\Tests\TestCase;

class LoadBalancersTest extends TestCase
{
    /**
     * @var LoadBalancers
     */
    protected $loadBalancers;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadBalancers = new LoadBalancers($this->hetznerApi->getHttpClient());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancers.json')));
        $loadBalancers = $this->loadBalancers->all();

        $this->assertCount(1, $loadBalancers);
        $this->assertLastRequestEquals('GET', '/load_balancers');
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer.json')));
        $loadBalancer = $this->loadBalancers->get(4711);

        $this->assertEquals($loadBalancer->id, 4711);
        $this->assertEquals($loadBalancer->name, 'my-resource');

        $this->assertLastRequestEquals('GET', '/load_balancers/4711');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancers.json')));
        $loadBalancer = $this->loadBalancers->getByName('my-resource');

        $this->assertEquals($loadBalancer->id, 4711);
        $this->assertEquals($loadBalancer->name, 'my-resource');
        $this->assertLastRequestEquals('GET', '/load_balancers');
        $this->assertLastRequestQueryParametersContains('name', 'my-resource');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancers.json')));
        $loadBalancers = $this->loadBalancers->list()->load_balancers;

        $this->assertEquals(count($loadBalancers), 1);
        $this->assertEquals($loadBalancers[0]->id, 4711);
        $this->assertEquals($loadBalancers[0]->name, 'my-resource');
        $this->assertLastRequestEquals('GET', '/load_balancers');
    }
}
