<?php

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 2019-03-28
 * Time: 13:51.
 */

namespace LKDev\HetznerCloud\Models\Images;

use LKDev\HetznerCloud\RequestOpts;

class ImageRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public $name;

    /** @var string */
    public $architecture;

    /**
     * RequestOpts constructor.
     *
     * @param  string|null  $name
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @param  string|null  $labelSelector
     * @param  string|null  $architecture
     */
    public function __construct(?string $name = null, ?int $perPage = null, ?int $page = null, ?string $labelSelector = null, ?string $architecture = null)
    {
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
        $this->architecture = $architecture;
    }
}
