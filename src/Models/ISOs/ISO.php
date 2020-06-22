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
     * ISO constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $type
     */
    public function __construct(int $id, string $name = null, string $description = null, string $type = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\ISOs\ISO|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->id, $input->name, $input->description, $input->type);
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
