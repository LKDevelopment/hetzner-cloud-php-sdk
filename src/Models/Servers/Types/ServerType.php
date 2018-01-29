<?php

namespace LKDev\HetznerCloud\Models\Servers\Types;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class ServerType extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * ServerType constructor.
     *
     * @param int $serverTypeId
     * @param \LKDev\HetznerCloud\HetznerAPIClient $hetznerAPIClient
     * @param null $httpClient
     */
    public function __construct(int $serverTypeId, \LKDev\HetznerCloud\HetznerAPIClient $hetznerAPIClient, $httpClient = null)
    {
        $this->id = $serverTypeId;
        parent::__construct($hetznerAPIClient, $httpClient);
    }
}