<?php

namespace LKDev\Tests\Unit\Models\LoadBalancers;

use BadMethodCallException;
use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType;
use LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerTypes;
use LKDev\Tests\TestCase;

/**
 * Class LoadBalancerTypeTest.
 */
class LoadBalancerTypeTest extends TestCase
{
    /**
     * @var LoadBalancerType
     */
    protected $load_balancer_type;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new LoadBalancerTypes($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/loadBalancerType.json')));
        $this->load_balancer_type = $tmp->get(4711);
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->load_balancer_type->delete();
    }

    public function testUpdateThrowsException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->load_balancer_type->update([]);
    }
}
