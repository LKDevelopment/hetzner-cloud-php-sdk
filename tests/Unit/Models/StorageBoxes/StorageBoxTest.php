<?php

namespace LKDev\Tests\Unit\Models\StorageBoxes;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBox;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxAccessSettings;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxes;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxSnapshotPlanRequest;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxSubaccountAccessSettings;
use LKDev\Tests\TestCase;

class StorageBoxTest extends TestCase
{
    protected StorageBox $storageBox;

    public function setUp(): void
    {
        parent::setUp();
        $this->hetznerApi->setStorageHttpClient(
            new GuzzleClient($this->hetznerApi, ['handler' => $this->mockHandler])
        );
        $tmp = new StorageBoxes($this->hetznerApi->getStorageHttpClient());
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box.json')));
        $this->storageBox = $tmp->getById(1);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->delete();

        $this->assertNotNull($resp->action);
        $this->assertEquals('change_protection', $resp->action->command);
        $this->assertLastRequestEquals('DELETE', '/storage_boxes/1');
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/storage_box.json')));
        $resp = $this->storageBox->update(['name' => 'renamed-box']);

        $this->assertNotNull($resp->storage_box);
        $this->assertLastRequestEquals('PUT', '/storage_boxes/1');
        $this->assertLastRequestBodyParametersEqual(['name' => 'renamed-box']);
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->changeProtection(true);

        $this->assertNotNull($resp->action);
        $this->assertEquals('change_protection', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }

    public function testChangeType()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->changeType('bx21');

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/change_type');
        $this->assertLastRequestBodyParametersEqual(['storage_box_type' => 'bx21']);
    }

    public function testResetPassword()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->resetPassword('NewSecurePass123!');

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/reset_password');
        $this->assertLastRequestBodyParametersEqual(['password' => 'NewSecurePass123!']);
    }

    public function testUpdateAccessSettings()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $settings = new StorageBoxAccessSettings(false, false, true, false, false);
        $resp = $this->storageBox->updateAccessSettings($settings);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/update_access_settings');
        $this->assertLastRequestBodyParametersEqual([
            'reachable_externally' => false,
            'samba_enabled' => false,
            'ssh_enabled' => true,
            'webdav_enabled' => false,
            'zfs_enabled' => false,
        ]);
    }

    public function testEnableSnapshotPlan()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->enableSnapshotPlan(
            new StorageBoxSnapshotPlanRequest(5, 0, 2)
        );

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/enable_snapshot_plan');
    }

    public function testDisableSnapshotPlan()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->disableSnapshotPlan();

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/disable_snapshot_plan');
    }

    public function testRollbackSnapshot()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->rollbackSnapshot(20);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/actions/rollback_snapshot');
        $this->assertLastRequestBodyParametersEqual(['snapshot_id' => 20]);
    }

    public function testListActions()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/actions.json')));
        $actions = $this->storageBox->listActions();

        $this->assertCount(1, $actions);
        $this->assertEquals(101, $actions[0]->id);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/actions');
    }

    public function testGetAction()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $action = $this->storageBox->getAction(101);

        $this->assertEquals(101, $action->id);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/actions/101');
    }

    public function testListSubaccounts()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/subaccounts.json')));
        $subaccounts = $this->storageBox->listSubaccounts();

        $this->assertCount(1, $subaccounts);
        $this->assertEquals(10, $subaccounts[0]->id);
        $this->assertEquals('my-name', $subaccounts[0]->name);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/subaccounts');
    }

    public function testCreateSubaccount()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/subaccount_create.json')));
        $resp = $this->storageBox->createSubaccount('backups/server01', 'SubPass123!', 'backup-user');

        $this->assertNotNull($resp->action);
        $this->assertEquals('create_subaccount', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/subaccounts');
        $this->assertLastRequestBodyParametersEqual([
            'home_directory' => 'backups/server01',
            'password' => 'SubPass123!',
            'name' => 'backup-user',
        ]);
    }

    public function testCreateSubaccountWithAccessSettings()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/subaccount_create.json')));
        $settings = new StorageBoxSubaccountAccessSettings(ssh_enabled: true);
        $resp = $this->storageBox->createSubaccount('backups/server01', 'SubPass123!', 'backup-user', $settings);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/subaccounts');
        $this->assertLastRequestBodyParametersEqual([
            'home_directory' => 'backups/server01',
            'password' => 'SubPass123!',
            'name' => 'backup-user',
            'access_settings' => [
                'reachable_externally' => false,
                'readonly' => false,
                'samba_enabled' => false,
                'ssh_enabled' => true,
                'webdav_enabled' => false,
            ],
        ]);
    }

    public function testGetSubaccount()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/subaccount.json')));
        $sub = $this->storageBox->getSubaccount(10);

        $this->assertEquals(10, $sub->id);
        $this->assertEquals('my-name', $sub->name);
        $this->assertEquals('my_backups/host01.my.company', $sub->home_directory);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/subaccounts/10');
    }

    public function testUpdateSubaccount()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/subaccount.json')));
        $resp = $this->storageBox->updateSubaccount(10, name: 'renamed-user');

        $this->assertNotNull($resp->subaccount);
        $this->assertLastRequestEquals('PUT', '/storage_boxes/1/subaccounts/10');
        $this->assertLastRequestBodyParametersEqual(['name' => 'renamed-user']);
    }

    public function testDeleteSubaccount()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->deleteSubaccount(10);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('DELETE', '/storage_boxes/1/subaccounts/10');
    }

    public function testResetSubaccountPassword()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->resetSubaccountPassword(10, 'NewSubPass123!');

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/subaccounts/10/actions/reset_subaccount_password');
        $this->assertLastRequestBodyParametersEqual(['password' => 'NewSubPass123!']);
    }

    public function testChangeSubaccountHomeDirectory()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->changeSubaccountHomeDirectory(10, 'backups/server02');

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/subaccounts/10/actions/change_home_directory');
        $this->assertLastRequestBodyParametersEqual(['home_directory' => 'backups/server02']);
    }

    public function testUpdateSubaccountAccessSettings()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $settings = new StorageBoxSubaccountAccessSettings(ssh_enabled: true, readonly: true);
        $resp = $this->storageBox->updateSubaccountAccessSettings(10, $settings);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/subaccounts/10/actions/update_access_settings');
        $this->assertLastRequestBodyParametersEqual([
            'reachable_externally' => false,
            'readonly' => true,
            'samba_enabled' => false,
            'ssh_enabled' => true,
            'webdav_enabled' => false,
        ]);
    }

    public function testListSnapshots()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/snapshots.json')));
        $snapshots = $this->storageBox->listSnapshots();

        $this->assertCount(1, $snapshots);
        $this->assertEquals(20, $snapshots[0]->id);
        $this->assertEquals('before-migration', $snapshots[0]->description);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/snapshots');
    }

    public function testCreateSnapshot()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/snapshot_create.json')));
        $resp = $this->storageBox->createSnapshot('before-migration');

        $this->assertNotNull($resp->action);
        $this->assertEquals('create_snapshot', $resp->action->command);
        $this->assertLastRequestEquals('POST', '/storage_boxes/1/snapshots');
        $this->assertLastRequestBodyParametersEqual(['description' => 'before-migration']);
    }

    public function testGetSnapshot()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/snapshot.json')));
        $snap = $this->storageBox->getSnapshot(20);

        $this->assertEquals(20, $snap->id);
        $this->assertEquals('before-migration', $snap->description);
        $this->assertFalse($snap->is_automatic);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/snapshots/20');
    }

    public function testUpdateSnapshot()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/snapshot.json')));
        $resp = $this->storageBox->updateSnapshot(20, ['description' => 'updated-desc']);

        $this->assertNotNull($resp->snapshot);
        $this->assertLastRequestEquals('PUT', '/storage_boxes/1/snapshots/20');
        $this->assertLastRequestBodyParametersEqual(['description' => 'updated-desc']);
    }

    public function testDeleteSnapshot()
    {
        $this->mockHandler->append(new Response(201, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $resp = $this->storageBox->deleteSnapshot(20);

        $this->assertNotNull($resp->action);
        $this->assertLastRequestEquals('DELETE', '/storage_boxes/1/snapshots/20');
    }

    public function testListFolders()
    {
        $foldersJson = json_encode(['folders' => ['documents', 'backups', 'media']]);
        $this->mockHandler->append(new Response(200, [], $foldersJson));
        $folders = $this->storageBox->listFolders();

        $this->assertCount(3, $folders);
        $this->assertEquals('documents', $folders[0]);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/folders');
    }

    public function testListFoldersWithPath()
    {
        $foldersJson = json_encode(['folders' => ['server01', 'server02']]);
        $this->mockHandler->append(new Response(200, [], $foldersJson));
        $folders = $this->storageBox->listFolders('./backups');

        $this->assertCount(2, $folders);
        $this->assertLastRequestEquals('GET', '/storage_boxes/1/folders');
        $this->assertLastRequestQueryParametersContains('path', './backups');
    }
}
