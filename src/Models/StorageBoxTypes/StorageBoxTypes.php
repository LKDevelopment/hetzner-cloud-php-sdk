<?php

namespace LKDev\HetznerCloud\Models\StorageBoxTypes;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class StorageBoxTypes extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    public array $storage_box_types = [];

    /**
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(?GuzzleClient $httpClient = null)
    {
        $storageClient = $httpClient ?? (HetznerAPIClient::$instance ? HetznerAPIClient::$instance->getApiHetznerComClient() : null);
        parent::__construct($storageClient);
    }

    /**
     * Returns all Storage Box type objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-types/list_storage_box_types
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
     * Returns a page of Storage Box type objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-types/list_storage_box_types
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(?RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('storage_box_types'.$requestOpts->buildQuery());
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
     * Returns a specific Storage Box type by ID.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-types/get_storage_box_type
     *
     * @param  int  $id
     * @return StorageBoxType|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id): ?StorageBoxType
    {
        $response = $this->httpClient->get('storage_box_types/'.$id);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBoxType::parse(json_decode((string) $response->getBody())->storage_box_type);
        }

        return null;
    }

    /**
     * Returns a specific Storage Box type by name.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-types/list_storage_box_types
     *
     * @param  string  $name
     * @return StorageBoxType|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?StorageBoxType
    {
        $types = $this->list();

        foreach ($types->storage_box_types as $type) {
            if ($type->name === $name) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->storage_box_types = array_map(function ($type) {
            return StorageBoxType::parse($type);
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
        return ['one' => 'storage_box_type', 'many' => 'storage_box_types'];
    }
}
