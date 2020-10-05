<?php

namespace Tests\Unit\Models\Volumes;

use GuzzleHttp\Psr7\Response;
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

        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume.json')));
        $this->volume = $tmp->get(4711);
    }

    public function testAttach()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume_action_attach.json')));
        $resp = $this->volume->attach(new Server(42), false);
        $this->assertEquals('attach_volume', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/volumes/4711/actions/attach');
        $this->assertLastRequestBodyParametersEqual(['server' => 42, 'automount' => false]);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $resp = $this->volume->delete();

        $this->assertEmpty($resp->getResponse());
        $this->assertLastRequestEquals('DELETE', '/volumes/4711');
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume.json')));
        $this->volume->update(['name' => 'new-name']);
        $this->assertLastRequestEquals('PUT', '/volumes/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'new-name']);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume_action_change_protection.json')));
        $apiResponse = $this->volume->changeProtection(true);
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->volume->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('volume', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/volumes/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    public function testResize()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume_action_resize.json')));
        $resp = $this->volume->resize(50);
        $this->assertEquals('resize_volume', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/volumes/4711/actions/resize');
        $this->assertLastRequestBodyParametersEqual(['size' => 50]);
    }

    public function testDetach()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/volume_action_detach.json')));
        $resp = $this->volume->detach();
        $this->assertEquals('detach_volume', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/volumes/4711/actions/detach');
        $this->assertLastRequestBodyIsEmpty();
    }
}
