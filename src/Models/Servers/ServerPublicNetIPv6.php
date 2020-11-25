<?php

namespace LKDev\HetznerCloud\Models\Servers;

/**
 * Class ServerPublicNetIPv6.
 */
class ServerPublicNetIPv6
{
    /**
     * @var string
     */
    public $ip;
    /**
     * @var bool
     */
    public $blocked;
    /**
     * @var array
     */
    public $dnsPtr;

    /**
     * ServerPublicNetIPv6 constructor.
     * @param string $ip
     * @param bool $blocked
     * @param array $dnsPtr
     */
    public function __construct(string $ip, bool $blocked, array $dnsPtr)
    {
        $this->ip = $ip;
        $this->blocked = $blocked;
        $this->dnsPtr = $dnsPtr;
    }

    /**
     * @param \stdClass $data
     * @return ServerPublicNetIPv6
     */
    public static function parse(\stdClass $data)
    {
        $dnsPtrs = [];
        foreach ($data->dns_ptr as $dnsPtr) {
            $dnsPtrs[] = new ServerPublicNetIPv6DnsPtr($dnsPtr->ip, $dnsPtr->dns_ptr);
        }

        return new self($data->ip, $data->blocked, $dnsPtrs);
    }
}
