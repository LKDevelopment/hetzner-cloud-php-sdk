<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 08.08.18
 * Time: 07:58
 */

namespace Tests\Integration\FloatingIPs;

use LKDev\HetznerCloud\Models\FloatingIps\FloatingIp;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

/**
 * Class FloatingIpTest
 * @package Tests\tests\FloatingIPs
 */
class FloatingIpTest extends TestCase
{
    /**
     * @var FloatingIp
     */
    protected $floatingIp;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $tmp = new FloatingIps($this->hetznerApi->getHttpClient());

        $this->floatingIp = $tmp->get(1337);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeProtection()
    {
        $apiResponse = $this->floatingIp->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->floatingIp->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('floating_ip', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->assertTrue($this->floatingIp->delete());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeDescription()
    {
        $this->assertEquals($this->floatingIp->id, 4711);
        $this->assertEquals($this->floatingIp->description, 'Web Frontend');
        $result = $this->floatingIp->update(['description' => 'New description']);
        $this->assertEquals($result->description, 'New description');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAssign()
    {
        $apiResponse = $this->floatingIp->assignTo(new Server(42));
        $this->assertEquals('assign_floating_ip', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals(42, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
        $this->assertEquals($this->floatingIp->id, $apiResponse->getResponsePart('action')->resources[1]->id);
        $this->assertEquals('floating_ip', $apiResponse->getResponsePart('action')->resources[1]->type);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testUnassign()
    {
        $apiResponse = $this->floatingIp->unassign();
        $this->assertEquals('unassign_floating_ip', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals(42, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
        $this->assertEquals($this->floatingIp->id, $apiResponse->getResponsePart('action')->resources[1]->id);
        $this->assertEquals('floating_ip', $apiResponse->getResponsePart('action')->resources[1]->type);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeReverseDNS()
    {
        $apiResponse = $this->floatingIp->changeReverseDNS('1.2.3.4', 'server02.example.com');
        $this->assertEquals('change_dns_ptr', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->floatingIp->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('floating_ip', $apiResponse->getResponsePart('action')->resources[0]->type);
    }
}
