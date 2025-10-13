<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\Models\Protection;

class RRSet
{
    public string $id;
    public string $name;
    public string $type;
    public int $ttl;
    public array $records;
    public array $labels;
    public RRSetProtection $protection;

    /**
     * @param string $id
     * @param string $name
     * @param string $type
     * @param int $ttl
     * @param array $records
     * @param array|null $labels
     * @param RRSetProtection|null $protection
     */
    public function __construct(string $id, string $name, string $type, int $ttl, array $records, ?array $labels, ?RRSetProtection $protection)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->ttl = $ttl;
        $this->records = $records;
        $this->labels = $labels;
        $this->protection = $protection;
    }

    public static function fromResponse(array $data): RRSet
    {
        return new self($data['id'], $data['name'], $data['type'], $data['ttl'], $data['records'], $data['labels'], RRSetProtection::parse($data['protection']));
    }

    public function __toRequest(): array
    {
        $r = [
            "name" => $this->name,
            'type' => $this->type,
            'ttl' => $this->ttl,
            'records' => $this->records,
        ];
        if (!empty($this->labels)) {
            $r['labels'] = $this->labels;
        }
        return $r;
    }
}


class Record
{
    public string $value;
    public string $comment;


    public function __construct(string $value, string $comment)
    {
        $this->value = $value;
        $this->comment = $comment;
    }
}
