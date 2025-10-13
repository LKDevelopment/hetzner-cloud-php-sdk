<?php

namespace LKDev\HetznerCloud\Models\Zones;

use LKDev\HetznerCloud\RequestOpts;

/**
 * Class ServerRequestOpts.
 */
class ZoneRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $mode;

    /**
     * RequestOpts constructor.
     *
     * @param string|null $name
     * @param string|null $mode
     * @param int|null $perPage
     * @param int|null $page
     * @param string|null $labelSelector
     */
    public function __construct(?string $name = null, ?string $mode = null, ?int $perPage = null, ?int $page = null, ?string $labelSelector = null)
    {
        $this->name = $name;
        $this->mode = $mode;
        parent::__construct($perPage, $page, $labelSelector);
    }
}
