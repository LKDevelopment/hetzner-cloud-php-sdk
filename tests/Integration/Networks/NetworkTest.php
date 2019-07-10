<?php

namespace Tests\Integration\Networks;

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

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $tmp = new Networks($this->hetznerApi->getHttpClient());

        $this->network = $tmp->get(4711);
    }

    public function testAddSubnet()
    {
        $apiResponse = $this->network->addSubnet(new Subnet(Subnet::TYPE_SERVER, "10.0.1.0/24", "eu-central"));
        $this->assertEquals('add_subnet', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testDeleteSubnet()
    {
        $apiResponse = $this->network->deleteSubnet(new Subnet(Subnet::TYPE_SERVER, "10.0.1.0/24", "eu-central"));
        $this->assertEquals('delete_subnet', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testAddRoute()
    {
        $apiResponse = $this->network->addRoute(new Route("10.100.1.0/24", "10.0.1.1"));
        $this->assertEquals('add_route', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testDeleteRoute()
    {
        $apiResponse = $this->network->deleteRoute(new Route("10.100.1.0/24", "10.0.1.1"));
        $this->assertEquals('delete_route', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testChangeIPRange()
    {
        $apiResponse = $this->network->changeIPRange("10.0.0.0/12");
        $this->assertEquals('change_ip_range', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testChangeProtection()
    {
        $apiResponse = $this->network->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->network->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('network', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

}
