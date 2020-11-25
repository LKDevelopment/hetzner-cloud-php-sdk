<?php


namespace LKDev\HetznerCloud\Models\Servers;


/**
 * Class ServerPublicNet
 * @package LKDev\HetznerCloud\Models\Servers
 */
class ServerPublicNet
{
    /**
     * @var ServerPublicNetIPv4
     */
    public $ipv4;
    /**
     * @var ServerPublicNetIPv6
     */
    public $ipv6;
    /**
     * @var int[]
     */
    public $floatingIps;

    /**
     * ServerPublicNet constructor.
     * @param ServerPublicNetIPv4 $ipv4
     * @param ServerPublicNetIPv6 $ipv6
     * @param array $floatingIps
     */
    public function __construct(ServerPublicNetIPv4 $ipv4, ServerPublicNetIPv6 $ipv6, array $floatingIps)
    {
        $this->ipv4 = $ipv4;
        $this->ipv6 = $ipv6;
        $this->floatingIps = $floatingIps;
    }

    /**
     * @param \stdClass $data
     * @return ServerPublicNet
     */
    public static function parse(\stdClass $data)
    {
        return new ServerPublicNet(ServerPublicNetIPv4::parse($data->ipv4), ServerPublicNetIPv6::parse($data->ipv6), $data->floating_ips);
    }
}
