<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 19:51.
 */

namespace Tests\Integration\Servers;

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

        $this->server = $tmp->get(42);
    }

    public function testDisableBackups()
    {
        $apiResponse = $this->server->disableBackups();
        $this->assertEquals('disable_backup', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testReset()
    {
        $apiResponse = $this->server->reset();
        $this->assertEquals('reset_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testDisableRescue()
    {
        $apiResponse = $this->server->disableRescue();
        $this->assertEquals('disable_rescue', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testChangeProtection()
    {
        $apiResponse = $this->server->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testPowerOff()
    {
        $apiResponse = $this->server->powerOff();
        $this->assertEquals('stop_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testShutdown()
    {
        $apiResponse = $this->server->shutdown();
        $this->assertEquals('shutdown_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testSoftReboot()
    {
        $apiResponse = $this->server->softReboot();
        $this->assertEquals('reboot_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testRequestConsole()
    {
        $apiResponse = $this->server->requestConsole();
        $this->assertEquals('request_console', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('wss_url'));
    }

    public function testCreateImage()
    {
        $apiResponse = $this->server->createImage();
        $this->assertEquals('create_image', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertEquals(4711, $apiResponse->getResponsePart('image')->id);
    }

    public function testChangeType()
    {
        $apiResponse = $this->server->changeType(new ServerType(1, 'cx11'), true);
        $this->assertEquals('change_server_type', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testResetRootPassword()
    {
        $apiResponse = $this->server->resetRootPassword();
        $this->assertEquals('reset_password', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('root_password'));
    }

    public function testEnableRescue()
    {
        $apiResponse = $this->server->enableRescue();
        $this->assertEquals('enable_rescue', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testRebuildFromImage()
    {
        $apiResponse = $this->server->rebuildFromImage(new Image(4711, 'ubuntu', '', 'ubuntu-20.04'));
        $this->assertEquals('rebuild_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testAttachISO()
    {
        $apiResponse = $this->server->attachISO(new ISO(123, 'FreeBSD-11.0-RELEASE-amd64-dvd1'));
        $this->assertEquals('attach_iso', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testPowerOn()
    {
        $apiResponse = $this->server->powerOn();
        $this->assertEquals('start_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testEnableBackups()
    {
        $apiResponse = $this->server->enableBackups('22-02');
        $this->assertEquals('enable_backup', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testDetachISO()
    {
        $apiResponse = $this->server->detachISO();
        $this->assertEquals('detach_iso', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testChangeReverseDNS()
    {
        $apiResponse = $this->server->changeReverseDNS('127.0.0.1', 'hello.world');
        $this->assertEquals('change_dns_ptr', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testDelete()
    {
        $apiResponse = $this->server->delete();
        $this->assertEquals('delete_server', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);
    }

    public function testGet()
    {
        $server = $this->server->get();
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
    }

    public function testMetrics()
    {
        $apiResponse = $this->server->metrics('cpu,disk,network', date('c'), date('c'), 60);
        $metrics = $apiResponse->getResponsePart('metrics');
        $this->assertEquals([['1435781470.622', '42']], $metrics->time_series->name_of_timeseries->values ?? null);
    }

    public function testAttachToNetworkBasic()
    {
        $apiResponse = $this->server->attachToNetwork(new Network(4711));
        $this->assertEquals('attach_to_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertEquals(4711, $apiResponse->action->resources[1]->id);
        $this->assertEquals('network', $apiResponse->action->resources[1]->type);
    }

    public function testAttachToNetworkAdvanced()
    {
        $apiResponse = $this->server->attachToNetwork(new Network(4711), '10.0.1.1', ['10.0.1.2']);
        $this->assertEquals('attach_to_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertEquals(4711, $apiResponse->action->resources[1]->id);
        $this->assertEquals('network', $apiResponse->action->resources[1]->type);
    }

    public function testDetachFromNetwork()
    {
        $apiResponse = $this->server->detachFromNetwork(new Network(4711));
        $this->assertEquals('detach_from_network', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertEquals(4711, $apiResponse->action->resources[1]->id);
        $this->assertEquals('network', $apiResponse->action->resources[1]->type);
    }

    public function testChangeAliasIPs()
    {
        $apiResponse = $this->server->changeAliasIPs(new Network(4711), ['10.0.1.2']);
        $this->assertEquals('change_alias_ips', $apiResponse->action->command);
        $this->assertEquals($this->server->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('server', $apiResponse->action->resources[0]->type);

        $this->assertEquals(4711, $apiResponse->action->resources[1]->id);
        $this->assertEquals('network', $apiResponse->action->resources[1]->type);
    }
}
