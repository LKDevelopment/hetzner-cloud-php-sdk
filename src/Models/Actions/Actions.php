<?php

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;

/**
 *
 */
class Actions extends Model
{
    /**
     * @var
     */
    public $actions;

    /**
     * @var \LKDev\HetznerCloud\Models\Servers\Server
     */
    public $server;

    /**
     * Actions constructor.
     *
     * @param \LKDev\HetznerCloud\Models\Servers\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        parent::__construct();
    }

    /**
     * @return \LKDev\HetznerCloud\Models\Actions\Actions
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(): array
    {
        $response = $this->httpClient->get('servers/'.$this->server->id.'/actions');
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->server);
        }
    }

    /**
     * @param $actionId
     * @return \LKDev\HetznerCloud\Models\Actions\Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get($actionId): Action
    {
        $response = $this->httpClient->get('servers/'.$this->server->id.'/actions/'.$actionId);
        if (! HetznerAPIClient::hasError($response)) {
            return Action::parse(json_decode((string) $response->getBody()->action));
        }
    }

    /**
     * @param object $input
     * @return $this
     */
    public function setAdditionalData(object $input)
    {
        $this->actions = collect($input->actions)->map(function ($action, $key) {
            return Action::parse($action);
        })->toArray();

        return $this;
    }

    /**
     * @param object $input
     * @return $this|static
     */
    public static function parse(object $input)
    {
        return (new self())->setAdditionalData($input);
    }
}