<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 19:51.
 */

namespace Tests\Unit\Models\Servers;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\ISOs\ISO;
use LKDev\HetznerCloud\Models\Networks\Network;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use Tests\TestCase;

class ServerTest extends TestCase
{
    /**
     * @var Server
     */
    protected $server;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new Servers($this->hetznerApi->getHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server.json')));
        $this->server = $tmp->get(42);
    }

    public function testDisableBackups()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('disable_backup')));
        $apiResponse = $this->server->disableBackups();
        $this->assertEquals('disable_backup', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/servers/42/actions/disable_backup');
    }

    public function testReset()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('reset_server')));
        $apiResponse = $this->server->reset();
        $this->assertEquals('reset_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/reset');
    }

    public function testDisableRescue()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('disable_rescue')));
        $apiResponse = $this->server->disableRescue();
        $this->assertEquals('disable_rescue', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/disable_rescue');
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_protection')));
        $apiResponse = $this->server->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true, 'rebuild' => true]);
    }

    public function testPowerOff()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('stop_server')));
        $apiResponse = $this->server->powerOff();
        $this->assertEquals('stop_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/poweroff');
    }

    public function testShutdown()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('shutdown_server')));
        $apiResponse = $this->server->shutdown();
        $this->assertEquals('shutdown_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/shutdown');
    }

    public function testSoftReboot()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('reboot_server')));
        $apiResponse = $this->server->softReboot();
        $this->assertEquals('reboot_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/reboot');
    }

    public function testRequestConsole()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_action_request_console.json')));
        $apiResponse = $this->server->requestConsole();
        $this->assertEquals('request_console', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('wss_url'));
        $this->assertLastRequestEquals('POST', '/servers/42/actions/request_console');
    }

    public function testCreateImage()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_action_create_image.json')));
        $apiResponse = $this->server->createImage('My Snapshot');
        $this->assertEquals('create_image', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals(4711, $apiResponse->getResponsePart('image')->id);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/create_image');

        $this->assertLastRequestBodyParametersEqual(['description' => 'My Snapshot', 'type' => 'snapshot']);
    }

    public function testChangeType()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_server_type')));
        $apiResponse = $this->server->changeType(new ServerType(1, 'cx11'), true);
        $this->assertEquals('change_server_type', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/change_type');
        $this->assertLastRequestBodyParametersEqual(['server_type' => 'cx11', 'upgrade_disk' => true]);
    }

    public function testResetRootPassword()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_action_reset_password.json')));
        $apiResponse = $this->server->resetRootPassword();
        $this->assertEquals('reset_password', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('root_password'));
        $this->assertLastRequestEquals('POST', '/servers/42/actions/reset_password');
    }

    public function testEnableRescue()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_action_enable_rescue.json')));
        $apiResponse = $this->server->enableRescue();
        $this->assertEquals('enable_rescue', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/enable_rescue');
        $this->assertLastRequestBodyParametersEqual(['type' => 'linux64']);
    }

    public function testRebuildFromImage()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('rebuild_server')));
        $apiResponse = $this->server->rebuildFromImage(new Image(4711, 'ubuntu', '', 'ubuntu-20.04'));
        $this->assertEquals('rebuild_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/rebuild');
        $this->assertLastRequestBodyParametersEqual(['image' => 4711]);
    }

    public function testAttachISO()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('attach_iso')));
        $apiResponse = $this->server->attachISO(new ISO(123, 'FreeBSD-11.0-RELEASE-amd64-dvd1'));
        $this->assertEquals('attach_iso', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/attach_iso');
        $this->assertLastRequestBodyParametersEqual(['iso' => 'FreeBSD-11.0-RELEASE-amd64-dvd1']);
    }

    public function testPowerOn()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('start_server')));
        $apiResponse = $this->server->powerOn();
        $this->assertEquals('start_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/poweron');
    }

    public function testEnableBackups()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('enable_backup')));
        $apiResponse = $this->server->enableBackups('22-02');
        $this->assertEquals('enable_backup', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/enable_backup');
        $this->assertLastRequestBodyIsEmpty();
    }

    public function testDetachISO()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('detach_iso')));
        $apiResponse = $this->server->detachISO();
        $this->assertEquals('detach_iso', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/detach_iso');
        $this->assertLastRequestBodyIsEmpty();
    }

    public function testChangeReverseDNS()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_dns_ptr')));
        $apiResponse = $this->server->changeReverseDNS('127.0.0.1', 'hello.world');
        $this->assertEquals('change_dns_ptr', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/servers/42/actions/change_dns_ptr');
        $this->assertLastRequestBodyParametersEqual(['ip' => '127.0.0.1', 'dns_ptr' => 'hello.world']);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('delete_server')));
        $apiResponse = $this->server->delete();
        $this->assertEquals('delete_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('DELETE', '/servers/42');
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server.json')));
        $server = $this->server->reload();
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
    }

    public function testMetrics()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/server_action_metrics.json')));
        $apiResponse = $this->server->metrics('cpu,disk,network', date('c'), date('c'), 60);
        $metrics = $apiResponse->getResponsePart('metrics');
        $this->assertEquals([['1435781470.622', '42']], $metrics->time_series->name_of_timeseries->values ?? null);
        $this->assertLastRequestEquals('GET', '/servers/42/metrics');
        $this->assertLastRequestQueryParametersContains('type', 'cpu,disk,network');
        $this->assertLastRequestQueryParametersContains('start', date('c'));
        $this->assertLastRequestQueryParametersContains('end', date('c'));
        $this->assertLastRequestQueryParametersContains('step', 60);
    }

    public function testAttachToNetworkBasic()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('attach_to_network')));
        $apiResponse = $this->server->attachToNetwork(new Network(4711));
        $this->assertEquals('attach_to_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/servers/42/actions/attach_to_network');
        $this->assertLastRequestBodyParametersEqual(['network' => 4711]);
    }

    public function testAttachToNetworkAdvanced()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('attach_to_network')));
        $apiResponse = $this->server->attachToNetwork(new Network(4711), '10.0.1.1', ['10.0.1.2']);
        $this->assertEquals('attach_to_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/servers/42/actions/attach_to_network');
        $this->assertLastRequestBodyParametersEqual(['network' => 4711, 'ip' => '10.0.1.1', 'alias_ips' => ['10.0.1.2']]);
    }

    public function testDetachFromNetwork()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('detach_from_network')));
        $apiResponse = $this->server->detachFromNetwork(new Network(4711));
        $this->assertEquals('detach_from_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/servers/42/actions/detach_from_network');
        $this->assertLastRequestBodyParametersEqual(['network' => 4711]);
    }

    public function testChangeAliasIPs()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_alias_ips')));
        $apiResponse = $this->server->changeAliasIPs(new Network(4711), ['10.0.1.2']);
        $this->assertEquals('change_alias_ips', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/servers/42/actions/change_alias_ips');
        $this->assertLastRequestBodyParametersEqual(['network' => 4711, 'alias_ips' => ['10.0.1.2']]);
    }

    protected function getGenericActionResponse(string $command)
    {
        return str_replace('$command', $command, file_get_contents(__DIR__.'/fixtures/server_action_generic.json'));
    }
}
