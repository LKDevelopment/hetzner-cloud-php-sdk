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
     * @var string|null
     */
    public $selector;

    /**
     * FirewallResource constructor.
     *
     * @param string $type
     * @param Server|null $server
     * @param string|null $selector
     */
    public function __construct(string $type, ?Server $server = null, ?string $selector = null)
    {
        $this->type = $type;
        $this->server = $server;
        $this->selector = $selector;
    }


    /**
     * @return array
     */
    public function toRequestSchema(): array
    {
        $s = ['type' => $this->type];

        if ($this->type == self::TYPE_SERVER && $this->server !== null) {
            $s['server'] = ['id' => $this->server->id];
        } else if ($this->type == self::TYPE_LABEL_SELECTOR && $this->selector !== null) {
            $s['label_selector'] = ['selector' => $this->selector];
        }

        return $s;
    }
}
