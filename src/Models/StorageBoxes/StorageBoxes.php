<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;
use SensitiveParameter;

class StorageBoxes extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    public array $storage_boxes = [];

    /**
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(?GuzzleClient $httpClient = null)
    {
        $storageClient = $httpClient ?? (HetznerAPIClient::$instance ? HetznerAPIClient::$instance->getStorageHttpClient() : null);
        parent::__construct($storageClient);
    }

    /**
     * Returns all existing Storage Box objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/list_storage_boxes
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(?RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns a page of Storage Box objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/list_storage_boxes
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(?RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new StorageBoxRequestOpts();
        }
        $response = $this->httpClient->get('storage_boxes'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific Storage Box by ID.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/get_storage_box
     *
     * @param  int  $id
     * @return StorageBox|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id): ?StorageBox
    {
        $response = $this->httpClient->get('storage_boxes/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBox::parse(json_decode((string) $response->getBody())->storage_box);
        }

        return null;
    }

    /**
     * Returns a specific Storage Box by name.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/list_storage_boxes
     *
     * @param  string  $name
     * @return StorageBox|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?StorageBox
    {
        $boxes = $this->list(new StorageBoxRequestOpts($name));

        return (count($boxes->storage_boxes) > 0) ? $boxes->storage_boxes[0] : null;
    }

    /**
     * Creates a new Storage Box.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-boxes/create_storage_box
     *
     * @param  string  $name  Name of the Storage Box
     * @param  string  $location  ID or name of the location
     * @param  string  $storageBoxType  ID or name of the Storage Box type
     * @param  string  $password  Initial password (must meet the password policy)
     * @param  array  $labels  User-defined labels
     * @param  array  $sshKeys  SSH public keys to authorize
     * @param  StorageBoxAccessSettings|null  $accessSettings  Initial access settings
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $name,
        string $location,
        string $storageBoxType,
        #[SensitiveParameter]
        string $password,
        array $labels = [],
        array $sshKeys = [],
        ?StorageBoxAccessSettings $accessSettings = null
    ): ?APIResponse {
        $payload = [
            'name' => $name,
            'location' => $location,
            'storage_box_type' => $storageBoxType,
            'password' => $password,
        ];
        if (! empty($labels)) {
            $payload['labels'] = $labels;
        }
        if (! empty($sshKeys)) {
            $payload['ssh_keys'] = $sshKeys;
        }
        if ($accessSettings !== null) {
            $payload['access_settings'] = $accessSettings->toArray();
        }

        $response = $this->httpClient->post('storage_boxes', ['json' => $payload]);
        if (! HetznerAPIClient::hasError($response)) {
            $data = json_decode((string) $response->getBody());

            return APIResponse::create([
                'storage_box' => StorageBox::parse($data->storage_box),
                'action' => StorageBoxAction::parse($data->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->storage_boxes = array_map(function ($box) {
            return StorageBox::parse($box);
        }, $input);

        return $this;
    }

    /**
     * @param  $input
     * @return static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'storage_box', 'many' => 'storage_boxes'];
    }
}
