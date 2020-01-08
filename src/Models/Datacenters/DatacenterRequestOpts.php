<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 2019-03-28
 * Time: 13:51
 */

namespace LKDev\HetznerCloud\Models\Datacenters;


use LKDev\HetznerCloud\RequestOpts;

class DatacenterRequestOpts extends RequestOpts
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
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
    }
}
