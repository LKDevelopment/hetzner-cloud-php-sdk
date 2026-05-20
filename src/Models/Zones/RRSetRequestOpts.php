<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\RequestOpts;

/**
 * Class RRSetRequestOpts.
 */
class RRSetRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * RequestOpts constructor.
     *
     * @param  string|null  $name
     * @param  string|null  $type
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @param  string|null  $labelSelector
     */
    public function __construct(?string $name = null, ?string $type = null, ?int $perPage = null, ?int $page = null, ?string $labelSelector = null)
    {
        $this->name = $name;
        $this->type = $type;
        parent::__construct($perPage, $page, $labelSelector);
    }
}
