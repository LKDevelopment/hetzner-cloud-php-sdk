<?php

namespace Tests\Unit\Models\Networks;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Models\Networks\Network;
use LKDev\HetznerCloud\Models\Networks\Networks;
use LKDev\HetznerCloud\Models\Networks\Route;
use LKDev\HetznerCloud\Models\Networks\Subnet;
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

/**
 * Class NetworksTest.
 */
class NetworksTest extends TestCase
{
    /**
     * @var Networks
     */
    protected $networks;

    public function setUp(): void
    {
        parent::setUp();
        $this->networks = new Networks($this->hetznerApi->getHttpClient());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/networks.json')));
        $networks = $this->networks->all();
        $this->assertCount(1, $networks);

        $this->assertLastRequestEquals('GET', '/networks');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/networks.json')));
        $networks = $this->networks->list()->networks;
        $this->assertCount(1, $networks);
        $this->assertLastRequestEquals('GET', '/networks');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/networks.json')));
        $network = $this->networks->getByName('mynet');
        $this->assertEquals(4711, $network->id);
        $this->assertEquals('mynet', $network->name);
        $this->assertEquals('10.0.0.0/16', $network->ipRange);

        $this->assertCount(1, $network->subnets);
        $this->assertInstanceOf(Subnet::class, $network->subnets[0]);
        $this->assertCount(1, $network->routes);
        $this->assertInstanceOf(Route::class, $network->routes[0]);

        $this->assertCount(1, $network->servers);
        $this->assertInstanceOf(Server::class, $network->servers[0]);

        $this->assertInstanceOf(Protection::class, $network->protection);

        $this->assertEmpty($network->labels);

        $this->assertLastRequestEquals('GET', '/networks');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/network.json')));
        $network = $this->networks->get(4711);
        $this->assertEquals(4711, $network->id);
        $this->assertEquals('mynet', $network->name);
        $this->assertEquals('10.0.0.0/16', $network->ipRange);

        $this->assertCount(1, $network->subnets);
        $this->assertInstanceOf(Subnet::class, $network->subnets[0]);
        $this->assertCount(1, $network->routes);
        $this->assertInstanceOf(Route::class, $network->routes[0]);

        $this->assertCount(1, $network->servers);
        $this->assertInstanceOf(Server::class, $network->servers[0]);

        $this->assertInstanceOf(Protection::class, $network->protection);

        $this->assertEmpty($network->labels);

        $this->assertLastRequestEquals('GET', '/networks/4711');
    }

    public function testBasicCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/network.json')));
        $resp = $this->networks->create('mynet', '10.0.0.0/16');
        $this->assertInstanceOf(APIResponse::class, $resp);
        $this->assertInstanceOf(Network::class, $resp->getResponsePart('network'));

        $this->assertLastRequestEquals('POST', '/networks');
        $this->assertLastRequestBodyParametersEqual(['name' => 'mynet', 'ip_range' => '10.0.0.0/16']);
    }

    public function testAdvancedCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/network.json')));
        $resp = $this->networks->create('mynet', '10.0.0.0/16', [new Subnet(Subnet::TYPE_CLOUD, '10.0.1.0/24', 'eu-central')], [new Route('10.100.1.0/24', '10.0.1.1')]);
        $this->assertInstanceOf(APIResponse::class, $resp);
        $this->assertInstanceOf(Network::class, $resp->getResponsePart('network'));

        $this->assertLastRequestEquals('POST', '/networks');
        $this->assertLastRequestBodyParametersEqual(['name' => 'mynet', 'ip_range' => '10.0.0.0/16', 'subnets' => [['type' => 'cloud', 'ip_range' => '10.0.1.0/24', 'network_zone' => 'eu-central']], 'routes' => [['destination' => '10.100.1.0/24', 'gateway' => '10.0.1.1']]]);
    }
}
