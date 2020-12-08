<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:59.
 */

namespace LKDev\HetznerCloud\Models\FloatingIps;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Locations\Location;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class FloatingIps extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $floating_ips;

    /**
     * Returns all floating ip objects.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get
     * @param FloatingIPRequestOpts|RequestOpts|null $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new FloatingIPRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * Returns all floating ip objects.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get
     * @param FloatingIPRequestOpts|RequestOpts|null $requestOpts
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new FloatingIPRequestOpts();
        }
        $response = $this->httpClient->get('floating_ips'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string) $response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns a specific floating ip object.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get-1
     * @param int $locationId
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $floatingIpId): ?FloatingIp
    {
        $response = $this->httpClient->get('floating_ips/'.$floatingIpId);
        if (! HetznerAPIClient::hasError($response)) {
            return FloatingIp::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
        }

        return null;
    }

    /**
     * Returns a specific Floating IP object by its name.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-get-1
     * @param string $name
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name): ?FloatingIp
    {
        $resp = $this->list(new FloatingIPRequestOpts($name));

        return (count($resp->floating_ips) > 0) ? $resp->floating_ips[0] : null;
    }

    /**
     * Creates a new Floating IP assigned to a server.
     *
     * @see https://docs.hetzner.cloud/#resources-floating-ips-post
     * @param string $type
     * @param string|null $description
     * @param \LKDev\HetznerCloud\Models\Locations\Location|null $location
     * @param \LKDev\HetznerCloud\Models\Servers\Server|null $server
     * @param string|null $name
     * @param array $labels
     * @return \LKDev\HetznerCloud\Models\FloatingIps\FloatingIp|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $type,
        string $description = null,
        Location $location = null,
        Server $server = null,
        string $name = null,
        array $labels = []
    ): ?FloatingIp {
        $parameters = [
            'type' => $type,
        ];
        if ($description != null) {
            $parameters['description'] = $description;
        }
        if ($name != null) {
            $parameters['name'] = $name;
        }
        if ($location != null) {
            $parameters['home_location'] = $location->name;
        }
        if ($server != null) {
            $parameters['server'] = $server->id ?: $server->name;
        }
        if (!empty($labels)) {
            $parameters['labels'] = $labels;
        }
        $response = $this->httpClient->post('floating_ips', [
            'json' => $parameters,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return FloatingIp::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->floating_ips = collect($input)->map(function ($floatingIp, $key) {
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

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'floating_ip', 'many' => 'floating_ips'];
    }
}
