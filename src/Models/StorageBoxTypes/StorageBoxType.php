<?php

namespace LKDev\HetznerCloud\Models\StorageBoxTypes;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\StorageBoxTypes\StorageBoxTypePrice;

class StorageBoxType extends Model
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var int|null
     */
    public ?int $snapshot_limit;

    /**
     * @var int|null
     */
    public ?int $automatic_snapshot_limit;

    /**
     * @var int
     */
    public int $subaccounts_limit;

    /**
     * @var int
     */
    public int $size;

    /**
     * @var StorageBoxTypePrice[]
     */
    public ?array $prices;

    /**
     * @var object|null
     */
    public ?object $deprecation;

    /**
     * @param  GuzzleClient|null  $httpClient
     */
    public function __construct(?GuzzleClient $httpClient = null)
    {
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
        $this->description = $data->description;
        $this->snapshot_limit = $data->snapshot_limit ?? null;
        $this->automatic_snapshot_limit = $data->automatic_snapshot_limit ?? null;
        $this->subaccounts_limit = $data->subaccounts_limit;
        $this->size = $data->size;
        $this->prices = isset($data->prices) ? array_map(function ($price) {
            return StorageBoxTypePrice::parse($price);
        }, $data->prices) : null;
        $this->deprecation = $data->deprecation ?? null;

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

        return (new self())->setAdditionalData($input);
    }
}
