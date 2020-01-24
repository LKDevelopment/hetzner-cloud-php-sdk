<?php

namespace LKDev\HetznerCloud\Traits;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\RequestOpts;

trait GetFunctionTrait
{
    public function get($nameOrId)
    {
        try {
            return $this->getById((int) $nameOrId);
        } catch (\Exception $e) {
            unset($e);

            return $this->getByName($nameOrId);
        }
    }

    protected function _all(RequestOpts $requestOpts)
    {
        $entities = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        $max_pages = PHP_INT_MAX;
        for ($i = 1; $i < $max_pages; $i++) {
            $requestOpts->page = $i;
            $_f = $this->list($requestOpts);
            $entities = array_merge($entities, $_f->{$this->_getKeys()['many']});
            if ($_f->meta->pagination->page === $_f->meta->pagination->last_page) {
                $max_pages = 0;
            }
        }

        return $entities;
    }
}
