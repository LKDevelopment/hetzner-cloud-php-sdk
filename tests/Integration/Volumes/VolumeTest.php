<?php

namespace Tests\Integration\Volumes;

use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\Models\Volumes\Volume;
use LKDev\HetznerCloud\Models\Volumes\Volumes;
use Tests\TestCase;

class VolumeTest extends TestCase
{
    /**
     * @var Volume
     */
    protected $volume;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new Volumes($this->hetznerApi->getHttpClient());

        $this->volume = $tmp->get(4711);
    }

    public function testAttach()
    {
        $resp = $this->volume->attach(new Server(43), false);
        $this->assertEquals('attach_volume', $resp->action->command);
    }

    public function testDelete()
    {
        $resp = $this->volume->delete();
        $this->assertEmpty($resp->getResponse());
    }

    public function testUpdate()
    {
        $resp = $this->volume->update(['name' => 'new-name']);
        $this->assertEquals('new-name', $resp->getResponsePart('volume')->name);
    }

    public function testChangeProtection()
    {
        $apiResponse = $this->volume->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->action->command);
    }

    public function testResize()
    {
        $resp = $this->volume->resize(50);
        $this->assertEquals('resize_volume', $resp->action->command);
    }

    public function testDetach()
    {
        $resp = $this->volume->detach();
        $this->assertEquals('detach_volume', $resp->action->command);
    }
}
