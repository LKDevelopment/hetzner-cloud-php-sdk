<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\Clients\GuzzleClient;
use LKDev\HetznerCloud\HetznerAPIClient;
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

    public function reload()
    {
        return HetznerAPIClient::$instance->zones()->getById($this->zone)->getRRSet($this->id);
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    public function changeProtection(RRSetProtection $protection)
    {
        // TODO: Implement changeProtection() method.
    }

    public function changeTTL(int $ttl)
    {
        // TODO: Implement changeTTL() method.
    }

    public function setRecords(array $records)
    {
// TODO: Implement setRecords() method.
    }

    public function addRecords(array $records)
    {
        // TODO: Implement addRecords() method.
    }

    public function removeRecords(array $records)
    {
        // TODO: Implement removeRecords() method.
    }
}
