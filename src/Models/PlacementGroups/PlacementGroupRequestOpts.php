<?php

namespace LKDev\HetznerCloud\Models\PlacementGroups;

use LKDev\HetznerCloud\RequestOpts;

/**
 * Class ServerRequestOpts.
 */
class PlacementGroupRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    public $type;

    /**
     * RequestOpts constructor.
     *
     * @param $name
     * @param $type
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(string $name = null, int $type = null, int $perPage = null, int $page = null, string $labelSelector = null)
    {
        $this->name = $name;
        $this->type = $type;
        parent::__construct($perPage, $page, $labelSelector);
    }


}
