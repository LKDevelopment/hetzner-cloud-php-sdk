<?php

namespace LKDev\Tests\Unit\Models\Networks;

use LKDev\HetznerCloud\Models\Networks\Subnet;
use LKDev\HetznerCloud\Models\Networks\Route;
use LKDev\Tests\TestCase;

class SubnetAndRouteTest extends TestCase
{
    public function testSubnetParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/network.json'));
        $subnets = Subnet::parse($tmp->network->subnets);
        $this->assertIsArray($subnets);
        $this->assertCount(1, $subnets);
        $subnet = $subnets[0];
        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertEquals('cloud', $subnet->type);
        $this->assertEquals('10.0.1.0/24', $subnet->ipRange);
        $this->assertEquals('eu-central', $subnet->networkZone);
        $this->assertEquals('10.0.0.1', $subnet->gateway);
    }

    public function testSubnetToRequestPayload()
    {
        $subnet = new Subnet('cloud', '10.0.1.0/24', 'eu-central');
        $payload = $subnet->__toRequestPayload();
        $this->assertEquals([
            'type' => 'cloud',
            'ip_range' => '10.0.1.0/24',
            'network_zone' => 'eu-central',
        ], $payload);
    }

    public function testRouteParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/network.json'));
        $routes = Route::parse($tmp->network->routes);
        $this->assertIsArray($routes);
        $this->assertCount(1, $routes);
        $route = $routes[0];
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('10.100.1.0/24', $route->destination);
        $this->assertEquals('10.0.1.1', $route->gateway);
    }

    public function testRouteToRequestPayload()
    {
        $route = new Route('10.100.1.0/24', '10.0.1.1');
        $payload = $route->__toRequestPayload();
        $this->assertEquals([
            'destination' => '10.100.1.0/24',
            'gateway' => '10.0.1.1',
        ], $payload);
    }
}
