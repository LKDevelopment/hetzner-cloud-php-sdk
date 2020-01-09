<?php

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class Actions extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var
     */
    protected $actions;

    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * @param RequestOpts $requestOpts
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('actions'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                'actions' => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }
    }

    /**
     * @param $actionId
     * @return \LKDev\HetznerCloud\Models\Actions\Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $actionId): Action
    {
        $response = $this->httpClient->get('actions/'.$actionId);
        if (! HetznerAPIClient::hasError($response)) {
            return Action::parse(json_decode((string) $response->getBody())->action);
        }
    }

    public function getByName(string $name)
    {
        throw new \BadMethodCallException('getByName is not possible on Actions');
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->actions = collect($input)->map(function ($action, $key) {
            return Action::parse($action);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @param Server $server
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }

    /**
     * Wait for an action to complete.
     *
     * @param Action $action
     * @param float $pollingInterval in seconds
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public static function waitActionCompleted(Action $action, $pollingInterval = 0.5)
    {
        while ($action->status == 'running') {
            usleep($pollingInterval * 1000000);
            $action = $action->refresh();
        }

        return $action->status == 'success';
    }

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'action', 'many' => 'actions'];
    }
}
