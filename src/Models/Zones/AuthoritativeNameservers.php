<?php

namespace LKDev\HetznerCloud\Models\Zones;

class AuthoritativeNameservers
{
    public array $assigned;
    public array $delegated;
    public string $delegation_last_check;
    public string $delegation_status;

    /**
     * @param array $assigned
     * @param array $delegated
     * @param string $delegation_last_check
     * @param string $delegation_status
     */
    public function __construct(array $assigned, array $delegated, string $delegation_last_check, string $delegation_status)
    {
        $this->assigned = $assigned;
        $this->delegated = $delegated;
        $this->delegation_last_check = $delegation_last_check;
        $this->delegation_status = $delegation_status;
    }

    public static function fromResponse(array $response): AuthoritativeNameservers
    {
        return new self($response['assigned'], $response['delegated'], $response['delegation_last_check'], $response['delegation_status']);
    }
}
