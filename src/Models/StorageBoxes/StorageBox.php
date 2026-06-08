<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxAccessSettings;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxSnapshotPlanRequest;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxSubaccountAccessSettings;
use LKDev\HetznerCloud\Models\StorageBoxes\StorageBoxStats;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxType;
use SensitiveParameter;

class StorageBox extends Model implements Resource
{
    /**
     * @var int
     */
    public ?int $id;

    /**
     * @var string
     */
    public ?string $name;

    /**
     * @var StorageBoxType
     */
    public ?StorageBoxType $storage_box_type;

    /**
     * @var Location
     */
    public ?Location $location;

    /**
     * @var StorageBoxAccessSettings
     */
    public ?StorageBoxAccessSettings $access_settings;

    /**
     * @var object|null
     */
    public ?object $snapshot_plan;

    /**
     * @var Protection
     */
    public ?Protection $protection;

    /**
     * @var array
     */
    public ?array $labels;

    /**
     * @var string
     */
    public ?string $status;

    /**
     * @var string|null
     */
    public ?string $username;

    /**
     * @var string|null
     */
    public ?string $server;

    /**
     * @var string|null
     */
    public ?string $system;

    /**
     * @var StorageBoxStats
     */
    public ?StorageBoxStats $stats;

    /**
     * @var string
     */
    public ?string $created;

    /**
     * @param  int|null         $id
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(?int $id = null, ?GuzzleClient $httpClient = null)
    {
        $this->id = $id;
        $storageClient = $httpClient ?? (HetznerAPIClient::$instance ? HetznerAPIClient::$instance->getStorageHttpClient() : null);
        parent::__construct($storageClient);
    }

    /**
     * @param  $data
     * @return $this
     */
    public function setAdditionalData($data)
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->storage_box_type = $data->storage_box_type ? StorageBoxType::parse($data->storage_box_type) : null;
        $this->location = $data->location ? Location::parse($data->location) : null;
        $this->access_settings = $data->access_settings ? StorageBoxAccessSettings::parse($data->access_settings) : null;
        $this->snapshot_plan = $data->snapshot_plan ?? null;
        $this->protection = $data->protection ? Protection::parse($data->protection) : null;
        $this->labels = isset($data->labels) ? get_object_vars($data->labels) : [];
        $this->status = $data->status;
        $this->username = $data->username ?? null;
        $this->server = $data->server ?? null;
        $this->system = $data->system ?? null;
        $this->stats = $data->stats ? StorageBoxStats::parse($data->stats) : null;
        $this->created = $data->created;

        return $this;
    }

    /**
     * @param  $input
     * @return static|null
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return (new self($input->id))->setAdditionalData($input);
    }

    /**
     * Reload the data of the Storage Box.
     *
     * @return static
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->storageBoxes()->getById($this->id);
    }

    /**
     * Deletes a Storage Box. This immediately removes the Storage Box and all its data.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/delete_storage_box
     *
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): ?APIResponse
    {
        $response = $this->httpClient->delete('storage_boxes/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Updates a Storage Box. Currently supports renaming and updating labels.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/update_storage_box
     *
     * @param  array  $data  Keys: name (string), labels (object)
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?APIResponse
    {
        $response = $this->httpClient->put('storage_boxes/'.$this->id, ['json' => $data]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'storage_box' => self::parse(json_decode((string) $response->getBody())->storage_box),
            ], $response->getHeaders());
        }

        return null;
    }

    // --- Actions ---

    /**
     * Changes the delete protection of the Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/change_storage_box_protection
     *
     * @param  bool  $delete  Whether to enable delete protection
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/change_protection', [
            'json' => ['delete' => $delete],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Upgrades or downgrades a Storage Box to a different type.
     * Downgrading is only possible if current usage does not exceed the new type's capacity.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/change_storage_box_type
     *
     * @param  string  $storageBoxType  ID or name of the target Storage Box type
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeType(string $storageBoxType): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/change_type', [
            'json' => ['storage_box_type' => $storageBoxType],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Resets the password of the Storage Box.
     * The password must comply with the password policy (12–128 chars, mixed case, digits, special chars).
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/reset_storage_box_password
     *
     * @param  string  $password  New password
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function resetPassword(
        #[SensitiveParameter]
        string $password
    ): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/reset_password', [
            'json' => ['password' => $password],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Updates the access settings of the Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/update_storage_box_access_settings
     *
     * @param  StorageBoxAccessSettings  $settings
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function updateAccessSettings(StorageBoxAccessSettings $settings): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/update_access_settings', [
            'json' => $settings->toArray(),
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Enables automatic snapshots on a schedule.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/enable_storage_box_snapshot_plan
     *
     * @param  StorageBoxSnapshotPlanRequest  $schedule
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function enableSnapshotPlan(StorageBoxSnapshotPlanRequest $schedule): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/enable_snapshot_plan', [
            'json' => $schedule->toArray(),
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Disables the automatic snapshot plan.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/disable_storage_box_snapshot_plan
     *
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function disableSnapshotPlan(): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/disable_snapshot_plan');
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Restores the Storage Box to the state of a given snapshot.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/rollback_storage_box_snapshot
     *
     * @param  int  $snapshotId  ID of the snapshot to restore from
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function rollbackSnapshot(int $snapshotId): ?APIResponse
    {
        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/actions/rollback_snapshot', [
            'json' => ['snapshot_id' => $snapshotId],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    // --- Per-box action listing ---

    /**
     * Returns all actions for this Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/list_storage_box_actions
     *
     * @return StorageBoxAction[]
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function listActions(): array
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/actions');
        if (! HetznerAPIClient::hasError($response)) {
            return array_map(function ($action) {
                return StorageBoxAction::parse($action);
            }, json_decode((string) $response->getBody())->actions);
        }

        return [];
    }

    /**
     * Returns a specific action for this Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/get_storage_box_action
     *
     * @param  int  $actionId
     * @return StorageBoxAction|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getAction(int $actionId): ?StorageBoxAction
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/actions/'.$actionId);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBoxAction::parse(json_decode((string) $response->getBody())->action);
        }

        return null;
    }

    // --- Subaccounts ---

    /**
     * Returns all subaccounts of this Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccounts/list_storage_box_subaccounts
     *
     * @return StorageBoxSubaccount[]
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function listSubaccounts(): array
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/subaccounts');
        if (! HetznerAPIClient::hasError($response)) {
            return array_map(function ($subaccount) {
                return StorageBoxSubaccount::parse($subaccount);
            }, json_decode((string) $response->getBody())->subaccounts);
        }

        return [];
    }

    /**
     * Creates a new subaccount for this Storage Box.
     * Subaccounts share the storage space of the parent Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccounts/create_storage_box_subaccount
     *
     * @param  string                                 $homeDirectory  Home directory of the subaccount (e.g. "backups/server01")
     * @param  string                                 $password       Password (must meet the password policy)
     * @param  string|null                            $name           Display name
     * @param  StorageBoxSubaccountAccessSettings|null $accessSettings Access settings for the subaccount
     * @param  string|null                            $description    Optional description
     * @param  array                                  $labels         User-defined labels
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function createSubaccount(
        string $homeDirectory,
        #[SensitiveParameter]
        string $password,
        ?string $name = null,
        ?StorageBoxSubaccountAccessSettings $accessSettings = null,
        ?string $description = null,
        array $labels = []
    ): ?APIResponse {
        $payload = [
            'home_directory' => $homeDirectory,
            'password' => $password,
        ];
        if ($name !== null) {
            $payload['name'] = $name;
        }
        if ($accessSettings !== null) {
            $payload['access_settings'] = $accessSettings->toArray();
        }
        if ($description !== null) {
            $payload['description'] = $description;
        }
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }

        $response = $this->httpClient->post('storage_boxes/'.$this->id.'/subaccounts', ['json' => $payload]);
        if (! HetznerAPIClient::hasError($response)) {
            $data = json_decode((string) $response->getBody());

            return APIResponse::create([
                'subaccount' => $data->subaccount,
                'action' => StorageBoxAction::parse($data->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific subaccount by ID.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccounts/get_storage_box_subaccount
     *
     * @param  int  $subaccountId
     * @return StorageBoxSubaccount|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getSubaccount(int $subaccountId): ?StorageBoxSubaccount
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBoxSubaccount::parse(json_decode((string) $response->getBody())->subaccount);
        }

        return null;
    }

    /**
     * Updates a subaccount.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccounts/update_storage_box_subaccount
     *
     * @param  int          $subaccountId
     * @param  string|null  $name         Display name
     * @param  string|null  $description  Optional description
     * @param  array|null   $labels       User-defined labels
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function updateSubaccount(
        int $subaccountId,
        ?string $name = null,
        ?string $description = null,
        ?array $labels = null
    ): ?APIResponse {
        $payload = [];
        if ($name !== null) {
            $payload['name'] = $name;
        }
        if ($description !== null) {
            $payload['description'] = $description;
        }
        if ($labels !== null) {
            $payload['labels'] = $labels;
        }

        $response = $this->httpClient->put('storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId, ['json' => $payload]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'subaccount' => StorageBoxSubaccount::parse(json_decode((string) $response->getBody())->subaccount),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Deletes a subaccount.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccounts/delete_storage_box_subaccount
     *
     * @param  int  $subaccountId
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function deleteSubaccount(int $subaccountId): ?APIResponse
    {
        $response = $this->httpClient->delete('storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Resets the password of a subaccount.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccount-actions/reset_storage_box_subaccount_password
     *
     * @param  int     $subaccountId
     * @param  string  $password  New password (must meet the password policy)
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function resetSubaccountPassword(
        int $subaccountId,
        #[SensitiveParameter]
        string $password
    ): ?APIResponse
    {
        $response = $this->httpClient->post(
            'storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId.'/actions/reset_subaccount_password',
            ['json' => ['password' => $password]]
        );
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the home directory of a subaccount.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccount-actions/change_storage_box_subaccount_home_directory
     *
     * @param  int     $subaccountId
     * @param  string  $homeDirectory  New home directory path
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeSubaccountHomeDirectory(int $subaccountId, string $homeDirectory): ?APIResponse
    {
        $response = $this->httpClient->post(
            'storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId.'/actions/change_home_directory',
            ['json' => ['home_directory' => $homeDirectory]]
        );
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Updates the access settings of a subaccount.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-subaccount-actions/update_storage_box_subaccount_access_settings
     *
     * @param  int                               $subaccountId
     * @param  StorageBoxSubaccountAccessSettings $settings
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function updateSubaccountAccessSettings(int $subaccountId, StorageBoxSubaccountAccessSettings $settings): ?APIResponse
    {
        $response = $this->httpClient->post(
            'storage_boxes/'.$this->id.'/subaccounts/'.$subaccountId.'/actions/update_access_settings',
            ['json' => $settings->toArray()]
        );
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    // --- Snapshots ---

    /**
     * Returns all snapshots of this Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-snapshots/list_storage_box_snapshots
     *
     * @return StorageBoxSnapshot[]
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function listSnapshots(): array
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/snapshots');
        if (! HetznerAPIClient::hasError($response)) {
            return array_map(function ($snapshot) {
                return StorageBoxSnapshot::parse($snapshot);
            }, json_decode((string) $response->getBody())->snapshots);
        }

        return [];
    }

    /**
     * Creates a new snapshot of this Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-snapshots/create_storage_box_snapshot
     *
     * @param  string|null  $description  Optional description
     * @param  array        $labels       User-defined labels
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function createSnapshot(?string $description = null, array $labels = []): ?APIResponse
    {
        $payload = [];
        if ($description !== null) {
            $payload['description'] = $description;
        }
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }

        $response = $this->httpClient->post(
            'storage_boxes/'.$this->id.'/snapshots',
            empty($payload) ? [] : ['json' => $payload]
        );
        if (! HetznerAPIClient::hasError($response)) {
            $data = json_decode((string) $response->getBody());

            return APIResponse::create([
                'snapshot' => $data->snapshot,
                'action' => StorageBoxAction::parse($data->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific snapshot by ID.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-snapshots/get_storage_box_snapshot
     *
     * @param  int  $snapshotId
     * @return StorageBoxSnapshot|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getSnapshot(int $snapshotId): ?StorageBoxSnapshot
    {
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/snapshots/'.$snapshotId);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBoxSnapshot::parse(json_decode((string) $response->getBody())->snapshot);
        }

        return null;
    }

    /**
     * Updates a snapshot's description or labels.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-snapshots/update_storage_box_snapshot
     *
     * @param  int    $snapshotId
     * @param  array  $data  Keys: description (string), labels (object)
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function updateSnapshot(int $snapshotId, array $data): ?APIResponse
    {
        $response = $this->httpClient->put('storage_boxes/'.$this->id.'/snapshots/'.$snapshotId, ['json' => $data]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'snapshot' => StorageBoxSnapshot::parse(json_decode((string) $response->getBody())->snapshot),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Deletes a snapshot.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-snapshots/delete_storage_box_snapshot
     *
     * @param  int  $snapshotId
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function deleteSnapshot(int $snapshotId): ?APIResponse
    {
        $response = $this->httpClient->delete('storage_boxes/'.$this->id.'/snapshots/'.$snapshotId);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => StorageBoxAction::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    // --- Folders ---

    /**
     * Returns the list of folder names at the given path within the Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/list_storage_box_folders
     *
     * @param  string  $path  Directory path to list (default: ".", i.e. the root)
     * @return string[]
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function listFolders(string $path = '.'): array
    {
        $query = $path !== '.' ? '?path='.urlencode($path) : '';
        $response = $this->httpClient->get('storage_boxes/'.$this->id.'/folders'.$query);
        if (! HetznerAPIClient::hasError($response)) {
            return json_decode((string) $response->getBody())->folders;
        }

        return [];
    }
}
