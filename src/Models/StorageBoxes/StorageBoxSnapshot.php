<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\Models\Model;

class StorageBoxSnapshot extends Model
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
     * @var string|null
     */
    public ?string $name;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @var object
     */
    public ?object $labels;

    /**
     * @var object
     */
    public ?object $stats;

    /**
     * @var bool
     */
    public ?bool $is_automatic;

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
        $this->name = $data->name ?? null;
        $this->description = $data->description ?? null;
        $this->labels = $data->labels ?? null;
        $this->stats = $data->stats ?? null;
        $this->is_automatic = $data->is_automatic ?? null;
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
