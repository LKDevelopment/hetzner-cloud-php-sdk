<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;

class StorageBoxAction extends Action
{
    /**
     * Reload the action from the Storage Box actions endpoint.
     *
     * Overrides the default reload to use the Storage Box API instead of the
     * Cloud API, so waitUntilCompleted() polls the correct endpoint.
     *
     * @return static
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->storageBoxActions()->getById($this->id);
    }

    /**
     * @param  $input
     * @return static|null
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self(
            $input->id,
            $input->command,
            $input->progress,
            $input->status,
            $input->started ?? null,
            $input->finished ?? null,
            $input->resources,
            $input->error ?? null
        );
    }
}
