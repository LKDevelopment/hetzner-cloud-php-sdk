<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\APIException;
use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
use LKDev\HetznerCloud\RequestOpts;

class Zone extends Model implements Resource
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $status;

    /**
     * @var string
     */
    public string $created;

    /**
     * @var string
     */
    public string $mode;
    /**
     * @var array<PrimaryNameserver>
     */
    public array $primary_nameservers;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public Protection|array $protection;

    /**
     * @var object
     */
    public array $labels;

    /**
     * @var int
     */
    public int $ttl;

    /**
     * @var int
     */
    public int $record_count;

    /**
     * @var string
     */
    public string $registrar;

    /**
     * @var AuthoritativeNameservers
     */
    public AuthoritativeNameservers $authoritative_nameservers;

    /**
     * @param int $zoneId
     * @param GuzzleClient|null $httpClient
     */
    public function __construct(int $zoneId, ?GuzzleClient $httpClient = null)
    {
        $this->id = $zoneId;
        parent::__construct($httpClient);
    }

    /**
     * @param  $data
     * @return \LKDev\HetznerCloud\Models\Zones\Zone
     */
    public function setAdditionalData($data)
    {
        $this->name = $data->name;
        $this->status = $data->status ?: null;
        $this->mode = $data->mode ?: null;
        $this->created = $data->created;
        $this->protection = $data->protection ? Protection::parse($data->protection) : new Protection(false);
        $this->labels = get_object_vars($data->labels);
        $this->record_count = $data->record_count;
        $this->ttl = $data->ttl;
        $this->registrar = $data->registrar;
        $this->authoritative_nameservers = AuthoritativeNameservers::fromResponse(get_object_vars($data->authoritative_nameservers));
        if (property_exists($data, 'primary_nameservers')) {
            $this->primary_nameservers = [];
            foreach ($data->primary_nameservers as $primary_nameserver) {
                $this->primary_nameservers[] = PrimaryNameserver::fromResponse(get_object_vars($primary_nameserver));
            }
        }

        return $this;
    }

    /**
     * Reload the data of the zone.
     *
     * @return zone
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->zones()->get($this->id);
    }

    /**
     * Deletes a zone. This immediately removes the zone from your account, and it is no longer accessible.
     *
     * @see https://docs.hetzner.cloud/reference/cloud#zones-delete-a-zone
     *
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): ?APIResponse
    {
        $response = $this->httpClient->delete($this->replaceZoneIdInUri('zones/{id}'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Update a zone with new meta data.
     *
     * @see https://docs.hetzner.cloud/reference/cloud#zones-update-a-zone
     *
     * @param array $data
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data)
    {
        $response = $this->httpClient->put($this->replaceZoneIdInUri('zones/{id}'), [
            'json' => $data,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'zone' => self::parse(json_decode((string)$response->getBody())->zone),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the protection configuration of the zone.
     *
     * @see https://docs.hetzner.cloud/#zone-actions-change-zone-protection
     *
     * @param bool $delete
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->id . '/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    public function exportZonefile(): ?APIResponse
    {
        $response = $this->httpClient->get('zones/' . $this->id . '/zonefile');
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'zonefile' => json_decode((string)$response->getBody())->zonefile,
            ], $response->getHeaders());
        }

        return null;
    }

    public function changeTTL(int $ttl): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->id . '/actions/change_ttl', [
            'json' => [
                'ttl' => $ttl,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    public function importZonefile(string $zonefile): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->id . '/actions/import_zonefile', [
            'json' => [
                'zonefile' => $zonefile,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function replaceZoneIdInUri(string $uri): string
    {
        return str_replace('{id}', $this->id, $uri);
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Zones\Zone|static |null
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return (new self($input->id))->setAdditionalData($input);
    }

    /**
     * @param array<PrimaryNameserver> $primary_nameservers
     * @return void#
     * @throws APIException
     */
    public function changePrimaryNameservers(array $primary_nameservers)
    {
        $response = $this->httpClient->post('zones/' . $this->id . '/actions/change_primary_nameservers', [
            'json' => [
                'primary_nameservers' => $primary_nameservers,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param RRSetRequestOpts|null $requestOpts
     * @return array<RRSet>
     * @throws APIException
     */
    public function allRRSets(?RRSetRequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RRSetRequestOpts();
        }
        $entities = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        $max_pages = PHP_INT_MAX;
        for ($i = 1; $i < $max_pages; $i++) {
            $requestOpts->page = $i;
            $_f = $this->listRRSets($requestOpts);
            $entities = array_merge($entities, $_f->rrsets);
            if ($_f->meta->pagination->page === $_f->meta->pagination->last_page || $_f->meta->pagination->last_page === null) {
                $max_pages = 0;
            }
        }

        return $entities;
    }

    /**
     * @param RRSetRequestOpts|null $requestOpts
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function listRRSets(?RRSetRequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RRSetRequestOpts();
        }
        $response = $this->httpClient->get('zones/' . $this->id . "/rrsets" . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string)$response->getBody());
            $rrsets = [];
            foreach ($resp->rrsets as $rrset) {
                $rrsets[] = RRSet::fromResponse(get_object_vars($rrset));
            }
            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                'rrsets' => $rrsets,
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array<Record> $records
     * @param int|null $ttl
     * @param array|null $labels
     * @return void
     * @throws APIException
     */
    public function createRRSet(string $name, string $type, array $records, ?int $ttl = null, ?array $labels = [])
    {
        $parameters = [
            'name' => $name,
            'type' => $type,
            'records' => $records,
        ];
        if ($ttl !== null) {
            $parameters['ttl'] = $ttl;
        }
        if (!empty($labels)) {
            $parameters['labels'] = $labels;
        }

        $response = $this->httpClient->post('zones/' . $this->id . '/rrsets', [
            'json' => $parameters,
        ]);

        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());

            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'rrset' => RRSet::fromResponse(get_object_vars($payload->rrset)),
            ], $response->getHeaders());
        }

        return null;
    }
}
