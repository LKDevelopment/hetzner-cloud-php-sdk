<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\Models\Model;

class StorageBoxSubaccount extends Model
{
    /**
     * @var int
     */
    public ?int $id;

    /**
     * @var int
     */
    public ?int $storage_box;

    /**
     * @var string
     */
    public ?string $name;

    /**
     * @var string
     */
    public ?string $home_directory;

    /**
     * @var StorageBoxSubaccountAccessSettings
     */
    public ?StorageBoxSubaccountAccessSettings $access_settings;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @var object
     */
    public ?object $labels;

    /**
     * @var string|null
     */
    public ?string $username;

    /**
     * @var string|null
     */
    public ?string $server;

    /**
     * @var string
     */
    public ?string $created;

    /**
     * @param  $data
     * @return $this
     */
    public function setAdditionalData($data)
    {
        $this->id = $data->id;
        $this->storage_box = $data->storage_box;
        $this->name = $data->name;
        $this->home_directory = $data->home_directory;
        $this->access_settings = isset($data->access_settings) ? StorageBoxSubaccountAccessSettings::parse($data->access_settings) : null;
        $this->description = $data->description ?? null;
        $this->labels = $data->labels ?? null;
        $this->username = $data->username ?? null;
        $this->server = $data->server ?? null;
        $this->created = $data->created ?? null;

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
