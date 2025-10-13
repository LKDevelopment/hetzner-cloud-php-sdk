<?php

namespace LKDev\Tests\Unit\Models\Zones;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Zones\Zone;
use LKDev\HetznerCloud\Models\Zones\Zones;
use LKDev\Tests\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @var Zone
     */
    protected $zone;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = new Zones($this->hetznerApi->getHttpClient());

        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/zone.json')));
        $this->zone = $tmp->getById(4711);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('delete_zone')));
        $resp = $this->zone->delete();

        $this->assertEquals('delete_zone', $resp->action->command);
        $this->assertEquals($this->zone->id, $resp->action->resources[0]->id);
        $this->assertEquals('zone', $resp->action->resources[0]->type);
        $this->assertLastRequestEquals('DELETE', '/zones/4711');
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/zone.json')));
        $this->zone->update(['name' => 'new-name']);
        $this->assertLastRequestEquals('PUT', '/zones/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'new-name']);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/zone_action_change_protection.json')));
        $apiResponse = $this->zone->changeProtection(true);
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->zone->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    protected function getGenericActionResponse(string $command)
    {
        return str_replace('$command', $command, file_get_contents(__DIR__.'/fixtures/zone_action_generic.json'));
    }
}
