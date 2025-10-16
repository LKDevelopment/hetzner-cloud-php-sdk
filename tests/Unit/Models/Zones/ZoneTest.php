<?php

namespace LKDev\Tests\Unit\Models\Zones;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Zones\PrimaryNameserver;
use LKDev\HetznerCloud\Models\Zones\Record;
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

        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone.json')));
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
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone.json')));
        $this->zone->update(['name' => 'new-name']);
        $this->assertLastRequestEquals('PUT', '/zones/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'new-name']);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_action_change_protection.json')));
        $apiResponse = $this->zone->changeProtection(true);
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($this->zone->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    public function testChangeTTL()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('change_ttl')));
        $apiResponse = $this->zone->changeTTL(50);
        $this->assertEquals('change_ttl', $apiResponse->action->command);
        $this->assertEquals($this->zone->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/actions/change_ttl');
        $this->assertLastRequestBodyParametersEqual(['ttl' => 50]);
    }

    public function testImportZonefile()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('import_zonefile')));
        $apiResponse = $this->zone->importZonefile('zonefile_content');
        $this->assertEquals('import_zonefile', $apiResponse->action->command);
        $this->assertEquals($this->zone->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/actions/import_zonefile');
        $this->assertLastRequestBodyParametersEqual(['zonefile' => 'zonefile_content']);
    }

    public function testTestChangePrimaryNameservers()
    {
        $this->mockHandler->append(new Response(200, [], $this->getGenericActionResponse('import_zonefile')));
        $apiResponse = $this->zone->changePrimaryNameservers([
            new PrimaryNameserver('192.168.178.1', 53),
        ]);
        $this->assertEquals('import_zonefile', $apiResponse->action->command);
        $this->assertEquals($this->zone->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('zone', $apiResponse->action->resources[0]->type);
        $this->assertLastRequestEquals('POST', '/zones/4711/actions/change_primary_nameservers');
        $this->assertLastRequestBodyParametersEqual(['primary_nameservers' => [['address' => '192.168.178.1', 'port' => 53]]]);
    }

    public function testExportZonefile()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_zonefile.json')));
        $apiResponse = $this->zone->exportZonefile();
        $this->assertNotEmpty($apiResponse->zonefile);
        $this->assertLastRequestEquals('GET', '/zones/4711/zonefile');
    }
    public function testAllRRSets()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_rrsets.json')));
        $rrsets = $this->zone->allRRSets();
        $this->assertCount(1, $rrsets);
        $rrset = $rrsets[0];
        $this->assertEquals($rrset->id, "www/A");
        $this->assertEquals($rrset->name, 'www');

        $this->assertLastRequestEquals('GET', '/zones/' . $this->zone->id . '/rrsets');
    }
    public function testListRRSets()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/zone_rrsets.json')));
        $rrsets = $this->zone->listRRSets()->rrsets;
        $this->assertCount(1, $rrsets);
        $rrset = $rrsets[0];
        $this->assertEquals($rrset->id, "www/A");
        $this->assertEquals($rrset->name, 'www');

        $this->assertLastRequestEquals('GET', '/zones/' . $this->zone->id . '/rrsets');
    }

    public function testCreateRRSet()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__ . '/fixtures/zone_create_rrset.json')));
        $apiResponse = $this->zone->createRRSet("www", "A", [new Record("198.51.100.1", "my webserver at Hetzner Cloud")], 3600, ["environment" => "prod"]);
        $this->assertNotEmpty($apiResponse->rrset);
        $this->assertNotEmpty($apiResponse->action);

        $this->assertLastRequestEquals('POST', '/zones/4711/rrsets');
        $this->assertLastRequestBodyParametersEqual([
            'name' => 'www',
            'type' => 'A',
            'ttl' => 3600,
            'labels' => ['environment' => 'prod'],
            'records' => [['value' => "198.51.100.1", 'comment' => "my webserver at Hetzner Cloud"]]]);
    }

    protected function getGenericActionResponse(string $command)
    {
        return str_replace('$command', $command, file_get_contents(__DIR__ . '/fixtures/zone_action_generic.json'));
    }
}
