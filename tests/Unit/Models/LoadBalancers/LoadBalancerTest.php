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
    protected $loadBalancer;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new LoadBalancers($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancer.json')));
        $this->loadBalancer = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeDescription()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/loadBalancer.json')));
        $this->assertEquals($this->loadBalancer->id, 4711);
        $this->assertEquals($this->loadBalancer->description, 'Web Frontend');
        $result = $this->loadBalancer->update(['description' => 'New description']);
        $this->assertLastRequestEquals('PUT', '/load_balancers/4711');
        $this->assertLastRequestBodyParametersEqual(['description' => 'New description']);
    }
}
