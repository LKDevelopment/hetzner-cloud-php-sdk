<?php

namespace LKDev\Tests\Unit\Models\Zones;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Zones\PrimaryNameserver;
use LKDev\HetznerCloud\Models\Zones\Record;
use LKDev\HetznerCloud\Models\Zones\RRSet;
use LKDev\HetznerCloud\Models\Zones\Zone;
use LKDev\HetznerCloud\Models\Zones\Zones;
use LKDev\Tests\TestCase;

class RRSetTest extends TestCase
{
    /**
     * @var RRSet
     */
    protected $rrset;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_rrset.json')));
        $this->rrset = (new Zone(4711))->getRRSetById('www/A');;
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('delete_rrset')));
        $resp = $this->rrset->delete();

        $this->assertEquals('delete_rrset', $resp->action->command);
        $this->assertEquals($this->rrset->zone, $resp->action->resources[0]->id);
        $this->assertEquals('zone', $resp->action->resources[0]->type);
        $this->assertLastRequestEquals('DELETE', '/zones/4711/rrsets/www/A');
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_rrset.json')));
        $this->rrset->update(['labels' => ['environment' => 'prod']]);;
        $this->assertLastRequestEquals('PUT', '/zones/4711/rrsets/www/A');
        $this->assertLastRequestBodyParametersEqual(['labels' => ['environment' => 'prod']]);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_rrset_protection')));;
        $apiResponse = $this->rrset->changeProtection(true);
        $this->assertEquals('change_rrset_protection', $apiResponse->action->command);
        $this->assertEquals($this->rrset->zone, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets/www/A/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['change' => true]);
    }

    public function testChangeTTL()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_rrset_ttl')));
        $apiResponse = $this->rrset->changeTTL(50);
        $this->assertEquals('change_rrset_ttl', $apiResponse->action->command);
        $this->assertEquals($this->rrset->zone, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets/www/A/actions/change_ttl');
        $this->assertLastRequestBodyParametersEqual(['ttl' => 50]);
    }

    public function testSetRecords()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('set_rrset_records')));
        $apiResponse = $this->rrset->setRecords([
            new Record('198.51.100.1', 'my webserver at Hetzner Cloud'),
        ]);
        $this->assertEquals('set_rrset_records', $apiResponse->action->command);
        $this->assertEquals($this->rrset->zone, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets/www/A/actions/set_records');
        $this->assertLastRequestBodyParametersEqual(['records' => [
            [
                "value" => "198.51.100.1",
                "comment" => "my webserver at Hetzner Cloud"
            ]
        ]]);
    }

    public function testAddRecords()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('add_rrset_records')));
        $apiResponse = $this->rrset->addRecords([
            new Record('198.51.100.1', 'my webserver at Hetzner Cloud'),
        ], 3600);
        $this->assertEquals('add_rrset_records', $apiResponse->action->command);
        $this->assertEquals($this->rrset->zone, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets/www/A/actions/add_records');
        $this->assertLastRequestBodyParametersEqual(['ttl' => 3600, 'records' => [
            [
                "value" => "198.51.100.1",
                "comment" => "my webserver at Hetzner Cloud"
            ]
        ]]);
    }

    public function testRemoveRecords()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('remove_rrset_records')));
        $apiResponse = $this->rrset->removeRecords([
            new Record('198.51.100.1', 'my webserver at Hetzner Cloud'),
        ]);
        $this->assertEquals('remove_rrset_records', $apiResponse->action->command);
        $this->assertEquals($this->rrset->zone, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets/www/A/actions/remove_records');
        $this->assertLastRequestBodyParametersEqual(['records' => [
            [
                "value" => "198.51.100.1",
                "comment" => "my webserver at Hetzner Cloud"
            ]
        ]]);
    }


    protected function getGenericActionResponse(string $command)
    {
        return str_replace('$command', $command, file_get_contents(__DIR__ . '/fixtures/zone_action_generic.json'));
    }
}
