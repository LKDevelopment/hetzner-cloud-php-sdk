<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:58
 */

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\Models\Model;

class ServerTypes extends Model
{
    public function all(): array
    {
        $this->httpClient->get('server_types');
    }
}