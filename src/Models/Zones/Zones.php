<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\APIException;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class Zones extends Model
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $zones;

    /**
     * @param  string  $name
     * @param  string  $mode
     * @param  int|null  $ttl
     * @param  array|null  $labels
     * @param  array<PrimaryNameserver>  $primary_nameservers
     * @param  array<RRSet>  $rrsets
     * @param  string|null  $zonefile
     * @return APIResponse|null
     *
     * @throws APIException
     */
    public function create(string $name, string $mode, ?int $ttl = null, ?array $labels = [], ?array $primary_nameservers = [], ?array $rrsets = [], ?string $zonefile = '')
    {
        $parameters = [
            'name' => $name,
            'mode' => $mode,
        ];
        if ($ttl !== null) {
            $parameters['ttl'] = $ttl;
        }
        if (! empty($labels)) {
            $parameters['labels'] = $labels;
        }
        if (! empty($rrsets)) {
            $parameters['rrsets'] = [];
            foreach ($rrsets as $rrset) {
                $parameters['rrsets'][] = $rrset->__toRequest();
            }
        }
        if (! empty($primary_nameservers)) {
            $parameters['primary_nameservers'] = $primary_nameservers;
        }

        if (! empty($zonefile)) {
            $parameters['zonefile'] = $zonefile;
        }
        $response = $this->httpClient->post('zones', [
            'json' => $parameters,
        ]);

        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'zone' => Zone::parse($payload->zone),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Returns all existing zone objects.
     *
     * @see https://docs.hetzner.cloud/#resources-zones-get
     *
     * @param  RequestOpts|null  $requestOpts
     * @return array
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(?RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new ZoneRequestOpts();
        }

        return $this->_all($requestOpts);
    }

    /**
     * List zone objects.
     *
     * @see https://docs.hetzner.cloud/#resources-zones-get
     *
     * @param  RequestOpts|null  $requestOpts
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(?RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new ZoneRequestOpts();
        }
        $response = $this->httpClient->get('zones'.$requestOpts->buildQuery());
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
     * Returns a specific zone object by its name. The zone must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-zones-get
     *
     * @param  string  $zoneName
     * @return \LKDev\HetznerCloud\Models\Zones\Zone|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $zoneName): ?Zone
    {
        $response = $this->httpClient->get('zones/'.$zoneName);
        if (! HetznerAPIClient::hasError($response)) {
            return Zone::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
        }

        return null;
    }

    /**
     * Returns a specific zone object by its id. The zone must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#resources-zones-get
     *
     * @param  int  $zoneId
     * @return \LKDev\HetznerCloud\Models\Zones\Zone|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $zoneId): ?Zone
    {
        $response = $this->httpClient->get('zones/'.$zoneId);
        if (! HetznerAPIClient::hasError($response)) {
            return Zone::parse(json_decode((string) $response->getBody())->{$this->_getKeys()['one']});
        }

        return null;
    }

    /**
     * Deletes a specific zone object by its id. The zone must exist inside the project.
     *
     * @see https://docs.hetzner.cloud/#zones-delete-a-zone
     *
     * @param  int  $zoneId
     * @return \LKDev\HetznerCloud\Models\Actions\Action|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function deleteById(int $zoneId): ?Action
    {
        $response = $this->httpClient->delete('zones/'.$zoneId);
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody());

            return Action::parse($payload->action);
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->zones = collect($input)
            ->map(function ($zone) {
                if ($zone != null) {
                    return Zone::parse($zone);
                }

                return null;
            })
            ->toArray();

        return $this;
    }

    /**
     * @param  $input
     * @return static
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
        return ['one' => 'zone', 'many' => 'zones'];
    }
}
