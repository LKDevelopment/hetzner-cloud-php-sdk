<?php

namespace LKDev\HetznerCloud\Models\Contracts;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\RequestOpts;

interface Resources
{
    public function all(RequestOpts $requestOpts = null): array;

    public function list(RequestOpts $requestOpts = null): APIResponse;

    public function getById(int $id);

    public function getByName(string $name);

    public function get($nameOrId);

    public function _getKeys(): array;
}
