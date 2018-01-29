<?php

namespace LKDev\HetznerCloud\Models\ISOs;

use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class ISO extends Model
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
    public function __construct(int $id = null, string $name = null, string $description = null, string $type = null)
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
            return null;
        }

        return new self($input->id, $input->name, $input->description, $input->type);
    }
}