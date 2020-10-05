<?php

namespace Tests\Unit\Models\Networks;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Networks\Network;
use LKDev\HetznerCloud\Models\Networks\Networks;
use LKDev\HetznerCloud\Models\Networks\Route;
use LKDev\HetznerCloud\Models\Networks\Subnet;
use Tests\TestCase;

class NetworkTest extends TestCase
{
    /**
     * @var Network
     */
    protected $network;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new Networks($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/network.json')));
        $this->network = $tmp->get(4711);
    }

    public function testAddSubnet()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('add_subnet')));
        $apiResponse = $this->network->addSubnet(new Subnet(Subnet::TYPE_CLOUD, '10.0.1.0/24', 'eu-central'));
        $this->assertEquals('add_subnet', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/add_subnet');
        $this->assertLastRequestBodyParametersEqual(['type' => 'cloud', 'ip_range' => '10.0.1.0/24', 'network_zone' => 'eu-central']);
    }

    public function testDeleteSubnet()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('delete_subnet')));
        $apiResponse = $this->network->deleteSubnet(new Subnet(Subnet::TYPE_CLOUD, '10.0.1.0/24', 'eu-central'));
        $this->assertEquals('delete_subnet', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/delete_subnet');
        $this->assertLastRequestBodyParametersEqual(['ip_range' => '10.0.1.0/24']);
    }

    public function testAddRoute()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('add_route')));
        $apiResponse = $this->network->addRoute(new Route('10.100.1.0/24', '10.0.1.1'));
        $this->assertEquals('add_route', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/add_route');
        $this->assertLastRequestBodyParametersEqual(['destination' => '10.100.1.0/24', 'gateway' => '10.0.1.1']);
    }

    public function testDeleteRoute()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('delete_route')));
        $apiResponse = $this->network->deleteRoute(new Route('10.100.1.0/24', '10.0.1.1'));
        $this->assertEquals('delete_route', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/delete_route');
        $this->assertLastRequestBodyParametersEqual(['destination' => '10.100.1.0/24', 'gateway' => '10.0.1.1']);
    }

    public function testChangeIPRange()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('change_ip_range')));
        $apiResponse = $this->network->changeIPRange('10.0.0.0/12');
        $this->assertEquals('change_ip_range', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/change_ip_range');
        $this->assertLastRequestBodyParametersEqual(['ip_range' => '10.0.0.0/12']);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], self::getGenericActionResponse('change_protection')));
        $apiResponse = $this->network->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->network->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('network', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/networks/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    protected function getGenericActionResponse(string $command)
    {
        return str_replace('$command', $command, file_get_contents(__DIR__.'/fixtures/network_action_generic.json'));
    }
}
