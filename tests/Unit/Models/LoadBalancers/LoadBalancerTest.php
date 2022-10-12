<?php

namespace LKDev\Tests\Unit\Models\LoadBalancers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancer;
use LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancers;
use LKDev\Tests\TestCase;

/**
 * Class LoadBalancerTest.
 */
class LoadBalancerTest extends TestCase
{
    /**
     * @var LoadBalancer
     */
    protected $load_balancer;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new LoadBalancers($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer.json')));
        $this->load_balancer = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer.json')));
        $this->assertEquals($this->load_balancer->id, 4711);
        $this->assertEquals($this->load_balancer->name, 'my-resource');
        $result = $this->load_balancer->update(['name' => 'my-resource']);
        $this->assertLastRequestEquals('PUT', '/load_balancers/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'my-resource']);
    }
}
