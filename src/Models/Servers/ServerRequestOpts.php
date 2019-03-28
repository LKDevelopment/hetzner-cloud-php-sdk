<?php

namespace LKDev\HetznerCloud\Models\Servers;


use LKDev\HetznerCloud\RequestOpts;

/**
 * Class ServerRequestOpts
 * @package LKDev\HetznerCloud\Models\Servers
 */
class ServerRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $status;

    /**
     * RequestOpts constructor.
     *
     * @param $name
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(string $name = null, string $status = null, int $perPage = null, int $page = null, string $labelSelector = null)
    {
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
        $this->status = $status;
    }
}
