<?php

namespace LKDev\HetznerCloud\Models\Firewalls;

use LKDev\HetznerCloud\Models\Servers\Server;

/**
 * Class FirewallResource.
 */
class FirewallResource
{
    const TYPE_SERVER = 'server';

    /**
     * @var string
     */
    public $type;
    /**
     * @var ?Server
     */
    public $server;

    /**
     * FirewallResource constructor.
     * @param string $type
     * @param Server|null $server
     */
    public function __construct(string $type, ?Server $server)
    {
        $this->type = $type;
        $this->server = $server;
    }

    /**
     * @return string[]
     */
    public function toRequestSchema(): array
    {
        $s = ['type' => $this->type];
        if ($this->type == self::TYPE_SERVER) {
            $s['server'] = ['id' => $this->server->id];
        }

        return $s;
    }
}
