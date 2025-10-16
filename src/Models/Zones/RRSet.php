<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class RRSet extends Model implements Resource
{
    public string $id;
    public string $name;
    public string $type;
    public int $ttl;
    public array $records;
    public array $labels;
    public ?RRSetProtection $protection;

    public int $zone;

    /**
     * @param string $id
     * @param GuzzleClient|null $client
     */
    public function __construct(string $id, ?GuzzleClient $client = null)
    {
        $this->id = $id;

        parent::__construct($client);
    }

    /**
     * @param  $data
     * @return \LKDev\HetznerCloud\Models\Zones\RRSet
     */
    public function setAdditionalData($data)
    {
        $this->name = $data->name;
        $this->type = $data->type;
        $this->ttl = $data->ttl;
        $this->records = $data->records;
        $this->labels = get_object_vars($data->labels);
        $this->protection = RRSetProtection::parse($data->protection);
        $this->zone = $data->zone;
        return $this;
    }

    public static function parse($input): RRSet
    {
        return (new self($input->id))->setAdditionalData($input);
    }

    public function __toRequest(): array
    {
        $r = [
            'name' => $this->name,
            'type' => $this->type,
            'ttl' => $this->ttl,
            'records' => $this->records,
        ];
        if (!empty($this->labels)) {
            $r['labels'] = $this->labels;
        }

        return $r;
    }

    /**
     * @return RRSet|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return (new Zone($this->zone))->getRRSetById($this->id);
    }

    /**
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): ?APIResponse
    {
        $response = $this->httpClient->delete('zones/' . $this->zone . '/rrsets/' . $this->id);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param array $data
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?APIResponse
    {
        $response = $this->httpClient->put('zones/' . $this->zone . '/rrsets/' . $this->id, [
            'json' => $data,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'rrset' => self::parse(json_decode((string)$response->getBody())->rrset),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param bool $change
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $change): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->zone . '/rrsets/' . $this->id . '/actions/change_protection', [
            'json' => [
                'change' => $change,
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
     * @param int $ttl
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeTTL(int $ttl): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->zone . '/rrsets/' . $this->id . '/actions/change_ttl', [
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

    /**
     * @param array<Record> $records
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function setRecords(array $records): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->zone . '/rrsets/' . $this->id . '/actions/set_records', [
            'json' => [
                'records' => $records,
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
     * @param array<Record> $records
     * @param int|null $ttl
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function addRecords(array $records, ?int $ttl = null): ?APIResponse
    {
        $response = $this->httpClient->post('zones/' . $this->zone . '/rrsets/' . $this->id . '/actions/add_records', [
            'json' => [
                'records' => $records,
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

    /**
     * @param array<Record> $records
     * @return APIResponse|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function removeRecords(array $records)
    {
        $response = $this->httpClient->post('zones/' . $this->zone . '/rrsets/' . $this->id . '/actions/remove_records', [
            'json' => [
                'records' => $records,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }
}
