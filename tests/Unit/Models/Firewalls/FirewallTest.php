<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 08.08.18
 * Time: 07:58.
 */

namespace Tests\Unit\Models\Firewalls;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Firewalls\Firewall;
use LKDev\HetznerCloud\Models\Firewalls\FirewallResource;
use LKDev\HetznerCloud\Models\Firewalls\FirewallRule;
use LKDev\HetznerCloud\Models\Firewalls\Firewalls;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

/**
 * Class FloatingIpTest.
 */
class FirewallTest extends TestCase
{
    /**
     * @var Firewall
     */
    protected $firewall;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new Firewalls($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall.json')));
        $this->firewall = $tmp->get(4711);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($this->firewall->delete());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall.json')));
        $this->assertEquals($this->firewall->id, 38);
        $this->assertEquals($this->firewall->name, 'Corporate Intranet Protection');
        $result = $this->firewall->update(['description' => 'New description']);
        $this->assertLastRequestEquals('PUT', '/firewalls/38');
        $this->assertLastRequestBodyParametersEqual(['description' => 'New description']);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testApplyToResources()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall_action_apply_to_resources.json')));
        $apiResponse = $this->firewall->applyToResources([new FirewallResource('server', new Server(42))]);
        $this->assertIsArray($apiResponse->actions);
        $this->assertCount(1, $apiResponse->actions);
        $this->assertEquals('apply_firewall', $apiResponse->actions[0]->command);
        $this->assertEquals(42, $apiResponse->actions[0]->resources[0]->id);
        $this->assertEquals('server', $apiResponse->actions[0]->resources[0]->type);
        $this->assertEquals($this->firewall->id, $apiResponse->actions[0]->resources[1]->id);
        $this->assertEquals('firewall', $apiResponse->actions[0]->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/firewalls/38/actions/apply_to_resources');
        $this->assertLastRequestBodyParametersEqual(['apply_to' => [['type' => 'server', 'server' => ['id' => 42]]]]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testRemoveFromResources()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall_action_remove_from_resources.json')));
        $apiResponse = $this->firewall->removeFromResources([new FirewallResource('server', new Server(42))]);
        $this->assertIsArray($apiResponse->actions);
        $this->assertCount(1, $apiResponse->actions);
        $this->assertEquals('remove_firewall', $apiResponse->actions[0]->command);
        $this->assertEquals(42, $apiResponse->actions[0]->resources[0]->id);
        $this->assertEquals('server', $apiResponse->actions[0]->resources[0]->type);
        $this->assertEquals($this->firewall->id, $apiResponse->actions[0]->resources[1]->id);
        $this->assertEquals('firewall', $apiResponse->actions[0]->resources[1]->type);
        $this->assertLastRequestEquals('POST', '/firewalls/38/actions/remove_from_resources');
        $this->assertLastRequestBodyParametersEqual(['remove_from' => [['type' => 'server', 'server' => ['id' => 42]]]]);
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testSetRules()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall_action_set_rules.json')));
        $apiResponse = $this->firewall->setRules([new FirewallRule('in', 'tcp', ['127.0.0.1/32'], [], '80')]);
        $this->assertIsArray($apiResponse->actions);
        $this->assertCount(2, $apiResponse->actions);
        $this->assertEquals('set_firewall_rules', $apiResponse->actions[0]->command);
        $this->assertEquals($this->firewall->id, $apiResponse->actions[0]->resources[0]->id);
        $this->assertEquals('firewall', $apiResponse->actions[0]->resources[0]->type);

        $this->assertEquals('apply_firewall', $apiResponse->actions[1]->command);
        $this->assertEquals($this->firewall->id, $apiResponse->actions[1]->resources[0]->id);
        $this->assertEquals('firewall', $apiResponse->actions[1]->resources[0]->type);
        $this->assertEquals(42, $apiResponse->actions[1]->resources[1]->id);
        $this->assertEquals('server', $apiResponse->actions[1]->resources[1]->type);

        $this->assertLastRequestEquals('POST', '/firewalls/38/actions/set_rules');
        $this->assertLastRequestBodyParametersEqual(['rules' => [['direction' => 'in', 'protocol' => 'tcp', 'source_ips' => ['127.0.0.1/32'], 'port' => 80]]]);
    }
}
