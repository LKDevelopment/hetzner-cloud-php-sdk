<?php

namespace LKDev\HetznerCloud\Models\Contracts;

interface Resource
{
    public function reload();

    public function delete();

    public function update(array $data);
}
