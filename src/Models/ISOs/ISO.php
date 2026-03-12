<?php

namespace LKDev\HetznerCloud\Models\ISOs;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class ISO extends Model implements Resource
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
    public $type;

    /**
     * @var string
     */
    public $architecture;

    /**
     * @var string|null
     */
    public $deprecated;

    /**
     * ISO constructor.
     *
     * @param  int  $id
     * @param  string  $name
     * @param  string  $description
     * @param  string  $type
     * @param  string  $architecture
     * @param  string|null  $deprecated
     */
    public function __construct(int $id, ?string $name = null, ?string $description = null, ?string $type = null, ?string $architecture = null, ?string $deprecated = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->architecture = $architecture;
        $this->deprecated = $deprecated;
        parent::__construct();
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\ISOs\ISO|static
     */
    public static function parse($input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->name, $input->description, $input->type, $input->architecture ?? null, $input->deprecated ?? null);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->isos()->get($this->id);
    }

    public function delete()
    {
        throw new \BadMethodCallException('delete on ISOs is not possible');
    }

    public function update(array $data)
    {
        throw new \BadMethodCallException('update on ISOs is not possible');
    }
}
