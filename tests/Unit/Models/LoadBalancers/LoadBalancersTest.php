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
        $location = $this->loadBalancers->get(4711);
        $this->assertEquals($location->id, 4711);
        $this->assertEquals($location->name, 'my-resource');

        $this->assertLastRequestEquals('GET', '/load_balancers/4711');
    }
}
