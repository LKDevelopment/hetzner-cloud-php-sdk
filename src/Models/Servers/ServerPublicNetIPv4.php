<?php


namespace LKDev\HetznerCloud\Models\Servers;


/**
 * Class ServerPublicNetIPv4
 * @package LKDev\HetznerCloud\Models\Servers
 */
class ServerPublicNetIPv4
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
     * @var string
     */
    public $dnsPtr;

    /**
     * ServerPublicNetIPv4 constructor.
     * @param string $ip
     * @param bool $blocked
     * @param string $dnsPtr
     */
    public function __construct(string $ip, bool $blocked, string $dnsPtr)
    {
        $this->ip = $ip;
        $this->blocked = $blocked;
        $this->dnsPtr = $dnsPtr;
    }

    /**
     * @param \stdClass $data
     * @return ServerPublicNetIPv4
     */
    public static function parse(\stdClass $data)
    {
        return new self($data->ip, $data->blocked, $data->dns_ptr);
    }

}
