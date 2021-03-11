<?php

namespace Tests\Unit\Models\Firewalls;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Models\Firewalls\Firewall;
use LKDev\HetznerCloud\Models\Firewalls\FirewallResource;
use LKDev\HetznerCloud\Models\Firewalls\FirewallRule;
use LKDev\HetznerCloud\Models\Firewalls\Firewalls;
use LKDev\HetznerCloud\Models\Servers\Server;
use Tests\TestCase;

/**
 * Class FirewallsTest.
 */
class FirewallsTest extends TestCase
{
    /**
     * @var Firewalls
     */
    protected $firewalls;

    public function setUp(): void
    {
        parent::setUp();
        $this->firewalls = new Firewalls($this->hetznerApi->getHttpClient());
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewalls.json')));
        $firewalls = $this->firewalls->all();
        $this->assertCount(1, $firewalls);

        $this->assertLastRequestEquals('GET', '/firewalls');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewalls.json')));
        $firewalls = $this->firewalls->list()->firewalls;
        $this->assertCount(1, $firewalls);
        $this->assertLastRequestEquals('GET', '/firewalls');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewalls.json')));
        $firewall = $this->firewalls->getByName('Corporate Intranet Protection');
        $this->assertEquals(38, $firewall->id);
        $this->assertEquals('Corporate Intranet Protection', $firewall->name);

        $this->assertCount(1, $firewall->rules);
        $this->assertInstanceOf(FirewallRule::class, $firewall->rules[0]);
        $this->assertEquals(FirewallRule::DIRECTION_IN, $firewall->rules[0]->direction);
        $this->assertEquals(FirewallRule::PROTOCOL_TCP, $firewall->rules[0]->protocol);
        $this->assertEquals('80', $firewall->rules[0]->port);
        $this->assertCount(3, $firewall->rules[0]->sourceIPs);
        $this->assertCount(3, $firewall->rules[0]->destinationIPs);

        $this->assertCount(1, $firewall->appliedTo);
        $this->assertInstanceOf(FirewallResource::class, $firewall->appliedTo[0]);

        $this->assertEmpty($firewall->labels);

        $this->assertLastRequestEquals('GET', '/firewalls');
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall.json')));
        $firewall = $this->firewalls->get(38);
        $this->assertEquals(38, $firewall->id);
        $this->assertEquals('Corporate Intranet Protection', $firewall->name);

        $this->assertCount(1, $firewall->rules);
        $this->assertInstanceOf(FirewallRule::class, $firewall->rules[0]);
        $this->assertCount(1, $firewall->appliedTo);
        $this->assertInstanceOf(FirewallResource::class, $firewall->appliedTo[0]);

        $this->assertEmpty($firewall->labels);

        $this->assertLastRequestEquals('GET', '/firewalls/38');
    }

    public function testBasicCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall_create.json')));
        $resp = $this->firewalls->create('Corporate Intranet Protection');
        $this->assertInstanceOf(APIResponse::class, $resp);
        $this->assertInstanceOf(Firewall::class, $resp->getResponsePart('firewall'));
        $this->assertIsArray($resp->getResponsePart('actions'));

        $this->assertLastRequestEquals('POST', '/firewalls');
        $this->assertLastRequestBodyParametersEqual(['name' => 'Corporate Intranet Protection']);
    }

    public function testAdvancedCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/firewall_create.json')));
        $resp = $this->firewalls->create('Corporate Intranet Protection', [new FirewallRule(FirewallRule::DIRECTION_IN, FirewallRule::PROTOCOL_TCP, ['127.0.0.1/32'], [], '80')], [new FirewallResource(FirewallResource::TYPE_SERVER, new Server(5))]);
        $this->assertInstanceOf(APIResponse::class, $resp);
        $this->assertInstanceOf(Firewall::class, $resp->getResponsePart('firewall'));
        $this->assertIsArray($resp->getResponsePart('actions'));

        $this->assertLastRequestEquals('POST', '/firewalls');
        $this->assertLastRequestBodyParametersEqual(['name' => 'Corporate Intranet Protection', 'rules' => [['direction' => 'in', 'protocol' => 'tcp', 'source_ips' => ['127.0.0.1/32'], 'port' => '80']], 'apply_to' => [['type' => 'server', 'server' => ['id' => 5]]]]);
    }
}
