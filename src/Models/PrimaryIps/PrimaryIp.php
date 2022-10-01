<?php

namespace LKDev\HetznerCloud\Models\PrimaryIps;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;

class PrimaryIp extends Model implements Resource
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
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $dns_ptr;

    /**
     * @var bool
     */
    public $blocked;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public $protection;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Datacenters\Datacenter
     */
    public $datacenter;

    /**
     * @var string
     */
    public $assignee_type;

    /**
     * @var int
     */
    public $assignee_id;

    /**
     * @var bool
     */
    public $auto_delete;

    /**
     * @param int $id
     * @param string $name
     * @param string $created
     * @param string $ip
     * @param string $type
     * @param array $dns_ptr
     * @param bool $blocked
     * @param array|Protection $protection
     * @param array $labels
     * @param array|\LKDev\HetznerCloud\Models\Datacenters\Datacenter $datacenter
     * @param string $assignee_type
     * @param int $assignee_id
     * @param bool $auto_delete
     */
    public function __construct(int $id, string $name, string $created, string $ip, string $type, array $dns_ptr, bool $blocked, $protection, array $labels, $datacenter, string $assignee_type, int $assignee_id, bool $auto_delete)
    {
        $this->id = $id;
        $this->name = $name;
        $this->created = $created;
        $this->ip = $ip;
        $this->type = $type;
        $this->dns_ptr = $dns_ptr;
        $this->blocked = $blocked;
        $this->protection = $protection;
        $this->labels = $labels;
        $this->datacenter = $datacenter;
        $this->assignee_type = $assignee_type;
        $this->assignee_id = $assignee_id;
        $this->auto_delete = $auto_delete;
        parent::__construct();
    }


    /**
     * Update the Primary IP.
     *
     * @see https://docs.hetzner.cloud/#primary-ips-update-a-primary-ip
     *
     * @param  array  $data
     * @return static|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?self
    {
        $response = $this->httpClient->put('primary_ips/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->primary_ip);
        }

        return null;
    }

    /**
     * The Primary IP may be assigned to a Server. In this case it is unassigned automatically.
     * The Server must be powered off (status off) in order for this operation to succeed.
     *
     * @see https://docs.hetzner.cloud/#primary-ips-delete-a-primary-ip
     *
     * @return bool
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('primary_ips/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\PrimaryIps\PrimaryIp|static|null
     */
    public static function parse($input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->name, $input->created, $input->ip, $input->type, $input->dns_ptr, $input->blocked, Protection::parse($input->protection), get_object_vars($input->labels), Datacenter::parse($input->datacenter), $input->assignee_type, $input->assignee_id, $input->auto_delete);
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->primaryIps()->get($this->id);
    }
}
