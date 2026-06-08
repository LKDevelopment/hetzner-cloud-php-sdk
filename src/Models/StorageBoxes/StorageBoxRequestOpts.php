<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\RequestOpts;

class StorageBoxRequestOpts extends RequestOpts
{
    /**
     * @var string
     */
    public ?string $name;

    public function __construct(
        ?string $name = null,
        ?int $perPage = null,
        ?int $page = null,
        ?string $labelSelector = null
    ) {
        parent::__construct($perPage, $page, $labelSelector);
        $this->name = $name;
    }
}
