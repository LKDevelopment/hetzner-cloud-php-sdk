<?php


namespace LKDev\HetznerCloud\Models\Servers;



class ServerPrivateNet
{
    /**
     * @var int
     */
    public $network;
    /**
     * @var string
     */
    public $ip;
    /**
     * @var string[]
     */
    public $aliasIps;
    /**
     * @var string
     */
    public $macAddress;

    /**
     * ServerPrivateNet constructor.
     * @param int $network
     * @param string $ip
     * @param string[] $aliasIps
     * @param string $macAddress
     */
    public function __construct(int $network, string $ip, array $aliasIps, string $macAddress)
    {
        $this->network = $network;
        $this->ip = $ip;
        $this->aliasIps = $aliasIps;
        $this->macAddress = $macAddress;
    }
}
