<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:52
 */

namespace LKDev\HetznerCloud\Models\Servers;

use LKDev\HetznerCloud\Models\Model;

class Servers extends Model
{

    public function all(){
        $this->httpClient->get('servers');

    }
}