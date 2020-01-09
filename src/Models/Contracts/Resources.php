<?php

namespace LKDev\HetznerCloud\Models\Contracts;

use LKDev\HetznerCloud\RequestOpts;

interface Resources
{
    public function all(RequestOpts $requestOpts = null): array;

    public function list(RequestOpts $requestOpts = null): array;

    public function getById(int $id);

    public function getByName(string $name);

    public function get($nameOrId);
}
