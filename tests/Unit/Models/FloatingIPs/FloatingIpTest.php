<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 08.08.18
 * Time: 07:58.
 */

namespace Tests\Unit\Models\FloatingIPs;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIp;
use LKDev\HetznerCloud\Models\FloatingIps\FloatingIps;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

/**
 * Class FloatingIpTest.
 */
class FloatingIpTest extends TestCase
{
    /**
     * @var FloatingIp
     */
    protected $floatingIp;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new FloatingIps($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $this->floatingIp = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP_action_change_protection.json')));
        $apiResponse = $this->floatingIp->changeProtection(true);
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->floatingIp->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('floating_ip', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/floating_ips/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($this->floatingIp->delete());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeDescription()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP.json')));
        $this->assertEquals($this->floatingIp->id, 4711);
        $this->assertEquals($this->floatingIp->description, 'Web Frontend');
        $result = $this->floatingIp->update(['description' => 'New description']);
        $this->assertLastRequestEquals('PUT', '/floating_ips/4711');
        $this->assertLastRequestBodyParametersEqual(['description' => 'New description']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAssign()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP_action_assign_floating_ip.json')));
        $apiResponse = $this->floatingIp->assignTo(new Server(42));
        $this->assertEquals('assign_floating_ip', $apiResponse->action->command);
        $this->assertEquals(42, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals($this->floatingIp->id, $apiResponse->action->resources[1]->id);
        $this->assertEquals('floating_ip', $apiResponse->action->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/floating_ips/4711/actions/assign');
        $this->assertLastRequestBodyParametersEqual(['server' => 42]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testUnassign()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP_action_unassign_floating_ip.json')));
        $apiResponse = $this->floatingIp->unassign();
        $this->assertEquals('unassign_floating_ip', $apiResponse->action->command);
        $this->assertEquals(42, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals($this->floatingIp->id, $apiResponse->action->resources[1]->id);
        $this->assertEquals('floating_ip', $apiResponse->action->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/floating_ips/4711/actions/unassign');
        $this->assertLastRequestBodyIsEmpty();
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeReverseDNS()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/floatingIP_action_change_dns_ptr.json')));
        $apiResponse = $this->floatingIp->changeReverseDNS('1.2.3.4', 'server02.example.com');
        $this->assertEquals('change_dns_ptr', $apiResponse->action->command);
        $this->assertEquals($this->floatingIp->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('floating_ip', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/floating_ips/4711/actions/change_dns_ptr');
        $this->assertLastRequestBodyParametersEqual(['ip' => '1.2.3.4', 'dns_ptr' => 'server02.example.com']);
    }
}
