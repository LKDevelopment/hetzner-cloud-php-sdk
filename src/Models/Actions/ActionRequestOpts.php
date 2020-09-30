<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 2019-03-28
 * Time: 13:51.
 */

namespace LKDev\HetznerCloud\Models\Actions;

use LKDev\HetznerCloud\RequestOpts;

class ActionRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $sort;

    /**
     * RequestOpts constructor.
     *
     * @param $status
     * @param $sort
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(string $status = null, string $sort = null, int $perPage = null, int $page = null, string $labelSelector = null)
    {
        parent::__construct($perPage, $page, $labelSelector);
        $this->status = $status;
        $this->sort = $sort;
    }
}
