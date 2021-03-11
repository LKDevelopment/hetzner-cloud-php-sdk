<?php

namespace LKDev\HetznerCloud\Models\Firewalls;

use LKDev\HetznerCloud\RequestOpts;

/**
 * Class FloatingIPRequestOpts.
 */
class FirewallRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * RequestOpts constructor.
     *
     * @param $name
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(string $name = null, int $perPage = null, int $page = null, string $labelSelector = null)
    {
        $this->name = $name;
        parent::__construct($perPage, $page, $labelSelector);
    }
}
