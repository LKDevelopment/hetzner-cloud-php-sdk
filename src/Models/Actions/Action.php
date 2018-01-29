<?php

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class Action extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $command;

    /**
     * @var int
     */
    public $progress;

    /**
     * @var string
     */
    public $started;

    /**
     * @var string
     */
    public $finished;

    /**
     * @var array
     */
    public $resources;

    /**
     * @var |null
     */
    public $error;

    /**
     * @var null|string
     */
    public $root_password = null;

    /**
     * Action constructor.
     *
     * @param int $id
     * @param string $command
     * @param int $progress
     * @param string $started
     * @param string $finished
     * @param array $resources
     * @param null| $error
     */
    public function __construct(
        int $id,
        string $command,
        int $progress,
        string $started,
        string $finished,
        $resources = null,
        $error = null,
        string $root_password = null
    ) {
        $this->id = $id;
        $this->command = $command;
        $this->progress = $progress;
        $this->started = $started;
        $this->finished = $finished;
        $this->resources = $resources;
        $this->error = $error;
        $this->root_password = $root_password;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\Actions\Action|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->command, $input->progress, $input->status, $input->started, $input->finished, $input->resources, $input->error, (property_exists($input, 'root_password') ? $input->root_password : null));
    }
}