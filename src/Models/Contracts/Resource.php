<?php


namespace LKDev\HetznerCloud\Models\Contracts;


use LKDev\HetznerCloud\RequestOpts;

interface Resource
{
    public function reload();

    public function delete();

    public function update(array $data);

}
