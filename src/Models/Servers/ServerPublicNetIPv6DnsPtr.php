<?php


namespace LKDev\HetznerCloud\Models\Servers;


/**
 * Class ServerPublicNetIPv6DnsPtr
 * @package LKDev\HetznerCloud\Models\Servers
 */
class ServerPublicNetIPv6DnsPtr
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $dnsPtr;

    /**
     * ServerPublicNetIPv6DnsPtr constructor.
     * @param string $ip
     * @param string $dnsPtr
     */
    public function __construct(string $ip, string $dnsPtr)
    {
        $this->ip = $ip;
        $this->dnsPtr = $dnsPtr;
    }



}
