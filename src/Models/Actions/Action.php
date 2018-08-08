<?php

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\HetznerAPIClient;
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
     * @var string
     */
    public $status;
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
     * @var null|string
     */
    public $wss_url = null;

    /**
     * Action constructor.
     *
     * @param int $id
     * @param string $command
     * @param int $progress
     * @param string $status
     * @param string $started
     * @param string $finished
     * @param array $resources
     * @param null| $error
     * @param string|null $root_password
     * @param string|null $wss_url
     */
    public function __construct(
        int $id,
        string $command,
        int $progress,
        string $status,
        string $started,
        string $finished = null,
        $resources = null,
        $error = null,
        string $root_password = null,
        string $wss_url = null
    )
    {
        $this->id = $id;
        $this->command = $command;
        $this->progress = $progress;
        $this->status = $status;
        $this->started = $started;
        $this->finished = $finished;
        $this->resources = $resources;
        $this->error = $error;
        parent::__construct();
    }

    /**
     * @param $actionId
     * @return Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById($actionId): Action
    {
        $response = $this->httpClient->get( 'actions/' . $actionId);
        if (!HetznerAPIClient::hasError($response)) {
            return Action::parse(json_decode((string)$response->getBody())->action);
        }
    }

    /**
     * @return Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function refresh(): Action
    {
        return $this->getById($this->id);
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

        return new self($input->id, $input->command, $input->progress, $input->status, $input->started, $input->finished, $input->resources, $input->error, (property_exists($input, 'root_password') ? $input->root_password : null), (property_exists($input, 'wss_url') ? $input->wss_url : null));
    }
}