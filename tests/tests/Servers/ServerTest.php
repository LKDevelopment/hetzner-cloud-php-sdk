<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 19:51
 */

namespace Tests\tests\Servers;

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
        $action = $this->server->disableBackups();
        $this->assertEquals('disable_backup', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testReset()
    {
        $action = $this->server->reset();
        $this->assertEquals('reset_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testDisableRescue()
    {
        $action = $this->server->disableRescue();
        $this->assertEquals('disable_rescue', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testChangeProtection()
    {
        $action = $this->server->changeProtection();
        $this->assertEquals('change_protection', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testPowerOff()
    {
        $action = $this->server->powerOff();
        $this->assertEquals('stop_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testShutdown()
    {
        $action = $this->server->shutdown();
        $this->assertEquals('shutdown_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testSoftReboot()
    {
        $action = $this->server->softReboot();
        $this->assertEquals('reboot_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testRequestConsole()
    {
        $action = $this->server->requestConsole();
        $this->assertEquals('request_console', $action->action->command);
        $this->assertEquals($this->server->id, $action->action->resources[0]->id);
        $this->assertEquals('server', $action->action->resources[0]->type);
        $this->assertNotNull($action->wss_url);
    }

    public function testCreateImage()
    {
        $action = $this->server->createImage();
        $this->assertEquals('create_image', $action->action->command);
        $this->assertEquals($this->server->id, $action->action->resources[0]->id);
        $this->assertEquals('server', $action->action->resources[0]->type);
        $this->assertEquals(4711, $action->image->id);
    }

    public function testChangeType()
    {
        $action = $this->server->changeType(new ServerType(1, 'cx11'), true);
        $this->assertEquals('change_server_type', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testResetRootPassword()
    {
        $action = $this->server->resetRootPassword();
        $this->assertEquals('reset_password', $action->action->command);
        $this->assertEquals($this->server->id, $action->action->resources[0]->id);
        $this->assertEquals('server', $action->action->resources[0]->type);
        $this->assertNotNull($action->root_password);
    }

    public function testEnableRescue()
    {
        $action = $this->server->enableRescue();
        $this->assertEquals('enable_rescue', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testRebuildFromImage()
    {
        $action = $this->server->rebuildFromImage(new Image(4711,'ubuntu','','ubuntu-16.04'));
        $this->assertEquals('rebuild_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testAttachISO()
    {
        $action = $this->server->attachISO(new ISO(123, 'FreeBSD-11.0-RELEASE-amd64-dvd1'));
        $this->assertEquals('attach_iso', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testPowerOn()
    {
        $action = $this->server->powerOn();
        $this->assertEquals('start_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testEnableBackups()
    {
        $action = $this->server->enableBackups('22-02');
        $this->assertEquals('enable_backup', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testDetachISO()
    {
        $action = $this->server->detachISO();
        $this->assertEquals('detach_iso', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testChangeReverseDNS()
    {
        $action = $this->server->changeReverseDNS('127.0.0.1', 'hello.world');
        $this->assertEquals('change_dns_ptr', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testDelete()
    {
        $action = $this->server->delete();
        $this->assertEquals('delete_server', $action->command);
        $this->assertEquals($this->server->id, $action->resources[0]->id);
        $this->assertEquals('server', $action->resources[0]->type);
    }

    public function testGet()
    {
        $server = $this->server->get();
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
    }
}
