<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:59
 */

namespace LKDev\HetznerCloud\Models\FloatingIps;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;

/**
 *
 */
class FloatingIp extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $ip;

    /**
     * @var int
     */
    public $server;

    /**
     * @var array
     */
    public $dnsPtr;

    /**
     * @var \LKDev\HetznerCloud\Models\Locations\Location
     */
    public $homeLocation;

    /**
     * @var bool
     */
    public $blocked;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public $protection;

    /**
     * FloatingIp constructor.
     *
     * @param int $id
     * @param string $description
     * @param string $ip
     * @param int $server
     * @param array $dnsPtr
     * @param \LKDev\HetznerCloud\Models\Locations\Location $homeLocation
     * @param bool $blocked
     * @param array $protection
     */
    public function __construct(
        int $id,
        string $description,
        string $ip,
        int $server,
        array $dnsPtr,
        Location $homeLocation,
        bool $blocked,
        Protection $protection
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->ip = $ip;
        $this->server = $server;
        $this->dnsPtr = $dnsPtr;
        $this->homeLocation = $homeLocation;
        $this->blocked = $blocked;
        $this->protection = $protection;
        parent::__construct();
    }

    /**
     * Changes the description of a Floating IP.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-put
     * @param string $description
     * @return static
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeDescription(string $description)
    {
        $response = $this->httpClient->put('floating_ips/'.$this->id, [
            'json' => [
                'description' => $description,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->floating_ip);
        }
    }

    /**
     * Deletes a Floating IP. If it is currently assigned to a server it will automatically get unassigned.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-delete
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete()
    {
        $response = $this->httpClient->delete('floating_ips/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }
    }

    /**
     * Changes the protection configuration of the Floating IP.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ip-actions-post-3
     * @param bool $delete
     * @return \LKDev\HetznerCloud\Models\Actions\Action
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): Action
    {
        $response = $this->httpClient->post('floating_ips/'.$this->id.'/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return Action::parse(json_decode((string) $response->getBody())->action);
        }
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->description, $input->ip, $input->type, $input->server, $input->dns_ptr, $input->home_location, $input->blocked, Protection::parse($input->protection));
    }
}