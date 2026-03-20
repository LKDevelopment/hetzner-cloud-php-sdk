<?php

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\Prices\ServerTypePrice;

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
     * @var array
     */
    public $price;

    /**
     * @var string
     */
    public $storageType;

    /**
     * @var string
     */
    public $cpuType;

    /**
     * @var string
     */
    public $architecture;

    /**
     * Deprecation info if the server type is deprecated, null otherwise.
     *
     * @var array{announced: string, unavailable_after: string}|null
     */
    public $deprecation;

    /**
     * ServerType constructor.
     *
     * @param  int  $serverTypeId
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
        $this->name = $input->name;
        $this->description = $input->description ?? null;
        $this->cores = $input->cores ?? null;
        $this->memory = $input->memory ?? null;
        $this->disk = $input->disk ?? null;
        $this->prices = Prices::parse($input->prices);
        $this->price = property_exists($input, 'price') ? ServerTypePrice::parse($input->price) : null;
        $this->storageType = $input->storage_type ?? null;
        $this->cpuType = $input->cpu_type ?? null;
        $this->architecture = property_exists($input, 'architecture') ? $input->architecture : null;
        $this->deprecation = (property_exists($input, 'deprecation') && $input->deprecation !== null) ? (array) $input->deprecation : null;

        return $this;
    }

    /**
     * @param  $input
     * @return self
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return (new self($input->id))->setAdditionalData($input);
    }
}
