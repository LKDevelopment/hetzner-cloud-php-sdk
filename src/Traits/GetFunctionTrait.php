<?php


namespace LKDev\HetznerCloud\Traits;


trait GetFunctionTrait
{
    public function get($nameOrId)
    {
        try {
            return $this->getById((int)$nameOrId);
        } catch (\Exception $e) {
            unset($e);
            return $this->getByName($nameOrId);
        }
    }
}
