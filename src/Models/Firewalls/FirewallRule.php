<?php

namespace LKDev\HetznerCloud\Models\Firewalls;

/**
 * Class FirewallRule.
 */
class FirewallRule
{
    const DIRECTION_IN = 'in';

    const DIRECTION_OUT = 'out';

    const PROTOCOL_TCP = 'tcp';

    const PROTOCOL_UDP = 'udp';

    const PROTOCOL_ICMP = 'icmp';
    /**
     * @var string
     */
    public $direction;
    /**
     * @var array<string>
     */
    public $sourceIPs;
    /**
     * @var array<string>
     */
    public $destinationIPs;
    /**
     * @var string
     */
    public $protocol;
    /**
     * @var string
     */
    public $port;

    /**
     * FirewallRule constructor.
     * @param string $direction
     * @param string[] $sourceIPs
     * @param string[] $destinationIPs
     * @param string $protocol
     * @param string $port
     */
    public function __construct(string $direction, string $protocol, array $sourceIPs = [], array $destinationIPs = [], string $port = '')
    {
        $this->direction = $direction;
        $this->sourceIPs = $sourceIPs;
        $this->destinationIPs = $destinationIPs;
        $this->protocol = $protocol;
        $this->port = $port;
    }

    /**
     * @return array
     */
    public function toRequestSchema(): array
    {
        $s = [
            'direction' => $this->direction,
            'source_ips' => $this->sourceIPs,
            'protocol' => $this->protocol,
        ];
        if (! empty($this->destinationIPs)) {
            $s['destination_ips'] = $this->destinationIPs;
        }
        if ($this->port != '') {
            $s['port'] = $this->port;
        }

        return $s;
    }
}
