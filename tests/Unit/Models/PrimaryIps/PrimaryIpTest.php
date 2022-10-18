<?php

namespace LKDev\Tests\Unit\Models\PrimaryIps;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\PrimaryIps\PrimaryIp;
use LKDev\HetznerCloud\Models\PrimaryIps\PrimaryIps;
use LKDev\Tests\TestCase;

/**
 * Class PrimaryIpTest.
 */
class PrimaryIpTest extends TestCase
{
    /**
     * @var PrimaryIp
     */
    protected $primaryIp;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new PrimaryIps($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP.json')));
        $this->primaryIp = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP_action_change_protection.json')));
        $apiResponse = $this->primaryIp->changeProtection(true);

        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->primaryIp->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('primary_ip', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/primary_ips/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($this->primaryIp->delete());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAssign()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP_action_assign_primary_ip.json')));
        $apiResponse = $this->primaryIp->assignTo(4711, 'server');

        $this->assertEquals('assign_primary_ip', $apiResponse->action->command);
        $this->assertEquals(42, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals($this->primaryIp->id, $apiResponse->action->resources[1]->id);
        $this->assertEquals('primary_ip', $apiResponse->action->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/primary_ips/4711/actions/assign');
        $this->assertLastRequestBodyParametersEqual(['assignee_id' => 4711, 'assignee_type' => 'server']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testUnassign()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP_action_unassign_primary_ip.json')));
        $apiResponse = $this->primaryIp->unassign();
        $this->assertEquals('unassign_primary_ip', $apiResponse->action->command);
        $this->assertEquals(42, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals($this->primaryIp->id, $apiResponse->action->resources[1]->id);
        $this->assertEquals('primary_ip', $apiResponse->action->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/primary_ips/4711/actions/unassign');
        $this->assertLastRequestBodyIsEmpty();
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testChangeReverseDNS()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/primaryIP_action_change_dns_ptr.json')));
        $apiResponse = $this->primaryIp->changeReverseDNS('1.2.3.4', 'server02.example.com');
        $this->assertEquals('change_dns_ptr', $apiResponse->action->command);
        $this->assertEquals($this->primaryIp->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('primary_ip', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/primary_ips/4711/actions/change_dns_ptr');
        $this->assertLastRequestBodyParametersEqual(['ip' => '1.2.3.4', 'dns_ptr' => 'server02.example.com']);
    }
}
