<?php

namespace LKDev\HetznerCloud\Models\LoadBalancerTypes;

use LKDev\HetznerCloud\RequestOpts;

class LoadBalancerTypeRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * LoadBalancerTypeRequestOpts constructor.
     *
     * @param  $name
     * @param  $perPage
     * @param  $page
     * @param  $labelSelector
     */
    public function __construct(string $name = null, int $perPage = null, int $page = null, string $labelSelector = null)
    {
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
    }
}
