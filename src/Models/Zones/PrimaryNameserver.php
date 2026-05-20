<?php

namespace LKDev\HetznerCloud\Models\Zones;

class PrimaryNameserver
{
    public string $address;
    public int $port;

    /**
     * @param  string  $address
     * @param  int  $port
     */
    public function __construct(string $address, int $port)
    {
        $this->port = $port;
        $this->address = $address;
    }

    public static function fromResponse(array $response): PrimaryNameserver
    {
        return new self($response['address'], $response['port']);
    }
}
