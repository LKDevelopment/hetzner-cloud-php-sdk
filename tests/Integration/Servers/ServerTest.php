<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 19:51
 */

namespace Tests\Integration\Servers;

use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\ISOs\ISO;
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

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $tmp = new Servers($this->hetznerApi->getHttpClient());

        $this->server = $tmp->get(42);
    }

    public function testDisableBackups()
    {
        $apiResponse = $this->server->disableBackups();
        $this->assertEquals('disable_backup', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testReset()
    {
        $apiResponse = $this->server->reset();
        $this->assertEquals('reset_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testDisableRescue()
    {
        $apiResponse = $this->server->disableRescue();
        $this->assertEquals('disable_rescue', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testChangeProtection()
    {
        $apiResponse = $this->server->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testPowerOff()
    {
        $apiResponse = $this->server->powerOff();
        $this->assertEquals('stop_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testShutdown()
    {
        $apiResponse = $this->server->shutdown();
        $this->assertEquals('shutdown_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testSoftReboot()
    {
        $apiResponse = $this->server->softReboot();
        $this->assertEquals('reboot_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testRequestConsole()
    {
        $apiResponse = $this->server->requestConsole();
        $this->assertEquals('request_console', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('wss_url'));
    }

    public function testCreateImage()
    {
        $apiResponse = $this->server->createImage();
        $this->assertEquals('create_image', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
        $this->assertEquals(4711, $apiResponse->getResponsePart('image')->id);
    }

    public function testChangeType()
    {
        $apiResponse = $this->server->changeType(new ServerType(1, 'cx11'), true);
        $this->assertEquals('change_server_type', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testResetRootPassword()
    {
        $apiResponse = $this->server->resetRootPassword();
        $this->assertEquals('reset_password', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
        $this->assertNotNull($apiResponse->getResponsePart('root_password'));
    }

    public function testEnableRescue()
    {
        $apiResponse = $this->server->enableRescue();
        $this->assertEquals('enable_rescue', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testRebuildFromImage()
    {
        $apiResponse = $this->server->rebuildFromImage(new Image(4711, 'ubuntu', '', 'ubuntu-16.04'));
        $this->assertEquals('rebuild_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testAttachISO()
    {
        $apiResponse = $this->server->attachISO(new ISO(123, 'FreeBSD-11.0-RELEASE-amd64-dvd1'));
        $this->assertEquals('attach_iso', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testPowerOn()
    {
        $apiResponse = $this->server->powerOn();
        $this->assertEquals('start_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testEnableBackups()
    {
        $apiResponse = $this->server->enableBackups('22-02');
        $this->assertEquals('enable_backup', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testDetachISO()
    {
        $apiResponse = $this->server->detachISO();
        $this->assertEquals('detach_iso', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testChangeReverseDNS()
    {
        $apiResponse = $this->server->changeReverseDNS('127.0.0.1', 'hello.world');
        $this->assertEquals('change_dns_ptr', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
    }

    public function testDelete()
    {
        $apiResponse = $this->server->delete();
        $this->assertEquals('delete_server', $apiResponse->getResponsePart('action')->command);
        $this->assertEquals($this->server->id, $apiResponse->getResponsePart('action')->resources[0]->id);
        $this->assertEquals('server', $apiResponse->getResponsePart('action')->resources[0]->type);
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
        $apiResponse = $this->server->metrics('cpu,disk,network', date("c"), date("c"), 60);
        $metrics = $apiResponse->getResponsePart('metrics');
        $this->assertEquals([["1435781470.622", "42"]], $metrics->time_series->name_of_timeseries->values ?? null);
    }
}
