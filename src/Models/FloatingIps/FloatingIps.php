<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:59
 */

namespace LKDev\HetznerCloud\Models\FloatingIps;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\RequestOpts;

class FloatingIps extends Model
{
    /**
     * @var array
     */
    public $floatingIps;

    /**
     * Returns all floating ip objects.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get
     * @param RequestOpts|null $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('floating_ips' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->floatingIps;
        }
    }

    /**
     * Returns a specific floating ip object.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get-1
     * @param int $locationId
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get(int $floatingIpId): FloatingIp
    {
        $response = $this->httpClient->get('floating_ips/' . $floatingIpId);
        if (!HetznerAPIClient::hasError($response)) {
            return FloatingIp::parse(json_decode((string)$response->getBody())->floating_ip);
        }
    }

    /**
     * Creates a new Floating IP assigned to a server.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-post
     * @param string $type
     * @param string|null $description
     * @param \LKDev\HetznerCloud\Models\Locations\Location|null $location
     * @param \LKDev\HetznerCloud\Models\Servers\Server|null $server
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $type,
        string $description = null,
        Location $location = null,
        Server $server = null
    ): FloatingIp
    {
        $response = $this->httpClient->post('floating_ips', [
            'type' => $type,
            'description' => $description,
            'server' => $server ?: $server->id,
            'home_location' => $location ?: $location->name,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return FloatingIp::parse(json_decode((string)$response->getBody())->floating_ip);
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->floatingIps = collect($input->floating_ips)->map(function ($floatingIp, $key) {
            return FloatingIp::parse($floatingIp);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
}