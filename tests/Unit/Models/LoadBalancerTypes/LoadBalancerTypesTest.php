<?php

namespace LKDev\Tests\Unit\Models\LoadBalancerTypes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerTypes;
use LKDev\Tests\TestCase;

class LoadBalancerTypesTest extends TestCase
{
    /**
     * @var LoadBalancerTypes
     */
    protected $load_balancer_types;

    public function setUp(): void
    {
        parent::setUp();
        $this->load_balancer_types = new LoadBalancerTypes($this->hetznerApi->getHttpClient());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancerTypes.json')));
        $loadBalancerTypes = $this->load_balancer_types->all();

        $this->assertCount(2, $loadBalancerTypes);
        $this->assertLastRequestEquals('GET', '/load_balancer_types');
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancerType.json')));
        $loadBalancer = $this->load_balancer_types->get(4711);

        $this->assertEquals($loadBalancer->id, 4711);
        $this->assertEquals($loadBalancer->name, 'lb11');

        $this->assertLastRequestEquals('GET', '/load_balancer_types/4711');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancerTypes.json')));
        $loadBalancer = $this->load_balancer_types->getByName('lb11');

        $this->assertEquals($loadBalancer->id, 4711);
        $this->assertEquals($loadBalancer->name, 'lb11');
        $this->assertLastRequestEquals('GET', '/load_balancer_types');
        $this->assertLastRequestQueryParametersContains('name', 'lb11');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancerTypes.json')));
        $loadBalancerTypes = $this->load_balancer_types->list()->load_balancer_types;

        $this->assertEquals(count($loadBalancerTypes), 2);
        $this->assertEquals($loadBalancerTypes[0]->id, 4711);
        $this->assertEquals($loadBalancerTypes[0]->name, 'lb11');
        $this->assertLastRequestEquals('GET', '/load_balancer_types');
    }
}
