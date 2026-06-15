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

class StorageBoxActions extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected array $actions = [];

    /**
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(?GuzzleClient $httpClient = null)
    {
        $storageClient = $httpClient ?? (HetznerAPIClient::$instance ? HetznerAPIClient::$instance->getApiHetznerComClient() : null);
        parent::__construct($storageClient);
    }

    /**
     * Returns all Storage Box action objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/list_storage_boxes_actions
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
     * Returns a page of Storage Box action objects.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/list_storage_boxes_actions
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
        $response = $this->httpClient->get('storage_boxes/actions'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                'actions' => self::parse($resp->actions)->actions,
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific Storage Box action by ID.
     *
     * @see https://docs.hetzner.cloud/reference/hetzner#tag/storage-box-actions/get_storage_boxes_action
     *
     * @param  int  $actionId
     * @return StorageBoxAction|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $actionId): ?StorageBoxAction
    {
        $response = $this->httpClient->get('storage_boxes/actions/'.$actionId);
        if (! HetznerAPIClient::hasError($response)) {
            return StorageBoxAction::parse(json_decode((string) $response->getBody())->action);
        }

        return null;
    }

    public function getByName(string $name)
    {
        throw new \BadMethodCallException('getByName is not possible on StorageBoxActions');
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->actions = array_map(function ($action) {
            return StorageBoxAction::parse($action);
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
        return ['one' => 'action', 'many' => 'actions'];
    }
}
