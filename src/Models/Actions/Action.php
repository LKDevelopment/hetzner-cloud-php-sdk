<?php

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class Action extends Model implements Resource
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
        $error = null
    ) {
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
     * @return Action|null
     * @throws \LKDev\HetznerCloud\APIException
     * @deprecated use Actions::getById instead
     */
    public function getById($actionId): ?self
    {
        $response = $this->httpClient->get('actions/'.$actionId);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->action);
        }

        return null;
    }

    /**
     * @return Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function refresh(): self
    {
        return $this->reload();
    }

    /**
     * Wait for an action to complete.
     * @param float $pollingInterval seconds
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function waitUntilCompleted($pollingInterval = 0.5)
    {
        return Actions::waitActionCompleted($this, $pollingInterval);
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->actions()->getById($this->id);
    }

    public function delete()
    {
        throw new \BadMethodCallException('delete on action is not possible');
    }

    public function update(array $data)
    {
        throw new \BadMethodCallException('update on action is not possible');
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\Actions\Action|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->id, $input->command, $input->progress, $input->status, $input->started, $input->finished, $input->resources, $input->error);
    }
}
