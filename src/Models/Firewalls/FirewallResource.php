<?php

namespace LKDev\HetznerCloud\Models\Firewalls;

use LKDev\HetznerCloud\Models\Servers\Server;

/**
 * Class FirewallResource.
 */
class FirewallResource
{
    const TYPE_SERVER = 'server';

    const TYPE_LABEL_SELECTOR = 'label_selector';

    /**
     * @var string
     */
    public $type;
    /**
     * @var ?Server
     */
    public $server;

    /**
     * @var ?array
     */
    public $labelSelector;

    /**
     * FirewallResource constructor.
     *
     * @param  string  $type
     * @param  Server|null  $server
     * @param  array|null  $labelSelector
     */
    public function __construct(string $type, ?Server $server = null, ?array $labelSelector = null)
    {
        $this->type = $type;
        $this->server = $server;
        $this->labelSelector = $labelSelector;
    }

    /**
     * @return array
     */
    public function toRequestSchema(): array
    {
        $s = ['type' => $this->type];
        if ($this->type == self::TYPE_SERVER) {
            $s['server'] = ['id' => $this->server->id];
        } elseif ($this->type == self::TYPE_LABEL_SELECTOR) {
            $s['label_selector'] = $this->labelSelector;
        }

        return $s;
    }
}
