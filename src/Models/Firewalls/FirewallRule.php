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
     * @var string
     */
    public $description;

    /**
     * FirewallRule constructor.
     *
     * @param  string  $direction
     * @param  string[]  $sourceIPs
     * @param  string[]  $destinationIPs
     * @param  string  $protocol
     * @param  string  $port
     * @param  string  $description
     */
    public function __construct(string $direction, string $protocol, array $sourceIPs = [], array $destinationIPs = [], ?string $port = '', ?string $description = '')
    {
        $this->direction = $direction;
        $this->sourceIPs = $sourceIPs;
        $this->destinationIPs = $destinationIPs;
        $this->protocol = $protocol;
        $this->port = $port;
        $this->description = $description;
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
        if ($this->description != '') {
            $s['description'] = $this->description;
        }

        return $s;
    }
}
