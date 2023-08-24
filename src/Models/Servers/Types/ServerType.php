<?php

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\Models\Model;

class ServerType extends Model
{

    /**
     * @var string
     */
    public $architecture;
    /**
     * @var string
     */
    public $cores;
    /**
     * @var string
     */
    public $cpuType;
    /**
     * @var bool
     */
    public $deprecated;
    /**
     * @var array
     */
    public $deprecation;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $disk;
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $includedTraffic;
    /**
     * @var string
     */
    public $memory;
    /**
     * @var string
     */
    public $name;
    /**
     * @var array
     */
    public $prices;

    /**
     * @var string
     */
    public $storageType;

    /**
     * ServerType constructor.
     *
     * @param int $serverTypeId
     */
    public function __construct(int $serverTypeId, string $name = '')
    {
        $this->id = $serverTypeId;
        $this->name = $name;
        parent::__construct();
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->architecture = $input->architecture;
        $this->cores = $input->cores;
        $this->cpuType = $input->cpu_type;
        $this->deprecated = (bool)$input->deprecated;
        $this->deprecation = $input->deprecation;
        $this->description = $input->description;
        $this->disk = $input->disk;
        $this->includedTraffic = $input->included_traffic;
        $this->memory = $input->memory;
        $this->name = $input->name;
        $this->prices = $input->prices;
        $this->storageType = $input->storage_type;

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
