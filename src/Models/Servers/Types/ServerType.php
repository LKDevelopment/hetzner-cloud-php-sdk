<?php

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class ServerType extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $cores;

    /**
     * @var string
     */
    public $memory;

    /**
     * @var string
     */
    public $disk;

    /**
     * @var array
     */
    public $prices;

    /**
     * @var string
     */
    public $storageType;

    /**
     * @var string
     */
    public $cpuType;

    /**
     * ServerType constructor.
     *
     * @param int $serverTypeId
     */
    public function __construct(int $serverTypeId)
    {
        $this->id = $serverTypeId;
        parent::__construct();
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->name = $input->name;
        $this->description = $input->description;
        $this->cores = $input->cores;
        $this->memory = $input->memory;
        $this->disk = $input->disk;
        $this->prices = $input->prices;
        $this->storageType = $input->storage_type;
        $this->cpuType = $input->cpu_type;
        return $this;
    }

    /**
     * @param  $input
     * @return self
     */
    public static function parse($input)
    {
        return (new self($input->id))->setAdditionalData($input);
    }
}