<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;

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
     * @param  int  $zoneId
     * @param  GuzzleClient|null  $httpClient
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
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Update a zone with new meta data.
     *
     * @see https://docs.hetzner.cloud/reference/cloud#zones-update-a-zone
     *
     * @param  array  $data
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data)
    {
        $response = $this->httpClient->put($this->replaceZoneIdInUri('zones/{id}'), [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'zone' => self::parse(json_decode((string) $response->getBody())->zone),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Changes the protection configuration of the zone.
     *
     * @see https://docs.hetzner.cloud/#zone-actions-change-zone-protection
     *
     * @param  bool  $delete
     * @return APIResponse|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): ?APIResponse
    {
        $response = $this->httpClient->post('zones/'.$this->id.'/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param  string  $uri
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
}
