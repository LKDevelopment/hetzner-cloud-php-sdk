<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\RequestOpts;

class LoadBalancerRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * LoadBalancerRequestOpts constructor.
     *
     * @param  $name
     * @param  $perPage
     * @param  $page
     * @param  $labelSelector
     */
    public function __construct(?string $name = null, ?int $perPage = null, ?int $page = null, ?string $labelSelector = null)
    {
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
    }
}
