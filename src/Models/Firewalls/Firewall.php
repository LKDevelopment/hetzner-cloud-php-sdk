<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:59.
 */

namespace LKDev\HetznerCloud\Models\Firewalls;

use LKDev\HetznerCloud\APIException;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;

class Firewall extends Model implements Resource
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
    public $created;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var FirewallRule[]
     */
    public $rules;

    /**
     * @var FirewallResource[]
     */
    public $appliedTo;

    /**
     * FloatingIp constructor.
     *
     * @param int $id
     * @param string $name
     * @param array $rules
     * @param array $appliedTo
     * @param array $labels
     * @param string $created
     */
    public function __construct(
        int $id,
        string $name = '',
        array $rules = [],
        array $appliedTo = [],
        array $labels = [],
        string $created = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->labels = $labels;
        $this->created = $created;
        $this->name = $name;
        $this->rules = $rules;
        $this->appliedTo = $appliedTo;
        parent::__construct();
    }

    /**
     * Update a Firewall.
     *
     * @see https://docs.hetzner.cloud/#firewalls-update-a-firewall
     * @param array $data
     * @return static|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?self
    {
        $response = $this->httpClient->put('firewalls/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->firewall);
        }

        return null;
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Firewalls\Firewall|static|null
     */
    public static function parse($input): ?self
    {
        if ($input == null) {
            return null;
        }
        $appliedTo = [];
        $rules = [];

        foreach ($input->rules as $r) {
            $rules[] = new FirewallRule($r->direction, $r->protocol, $r->source_ips, $r->destination_ips, $r->port);
        }

        foreach ($input->applied_to as $a) {
            $appliedTo[] = new FirewallResource($a->type, new Server($a->server->id));
        }

        return new self($input->id, $input->name, $rules, $appliedTo, get_object_vars($input->labels), $input->created);
    }

    /**
     * Sets the rules of a Firewall.
     *
     * @see https://docs.hetzner.cloud/#firewall-actions-set-rules
     * @param FirewallRule[] $rules
     * @return ?APIResponse|null
     * @throws APIException
     */
    public function setRules(array $rules): ?ApiResponse
    {
        $response = $this->httpClient->post('firewalls/'.$this->id.'/actions/set_rules', [
            'json' => [
                'rules' => collect($rules)->map(function ($r) {
                    return $r->toRequestSchema();
                }),
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'actions' => collect($payload->actions)->map(function ($action) {
                    return Action::parse($action);
                })->toArray(),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Deletes a Firewall.
     *
     * @see https://docs.hetzner.cloud/#firewalls-delete-a-firewall
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('firewalls/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    /**
     * Applies one Firewall to multiple resources.
     *
     * @see https://docs.hetzner.cloud/#firewall-actions-apply-to-resources
     * @param FirewallResource[] $resources
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function applyToResources(array $resources): ?APIResponse
    {
        $response = $this->httpClient->post('firewalls/'.$this->id.'/actions/apply_to_resources', [
            'json' => [
                'apply_to' => collect($resources)->map(function ($r) {
                    return $r->toRequestSchema();
                }),
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'actions' => collect($payload->actions)->map(function ($action) {
                    return Action::parse($action);
                })->toArray(),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Removes one Firewall from multiple resources.
     *
     * @see https://docs.hetzner.cloud/#firewall-actions-remove-from-resources
     * @param FirewallResource[] $resources
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function removeFromResources(array $resources): ?APIResponse
    {
        $response = $this->httpClient->post('firewalls/'.$this->id.'/actions/remove_from_resources', [
            'json' => [
                'remove_from' => collect($resources)->map(function ($r) {
                    return $r->toRequestSchema();
                }),
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'actions' => collect($payload->actions)->map(function ($action) {
                    return Action::parse($action);
                })->toArray(),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->firewalls()->get($this->id);
    }
}
