<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\Models\Model;

class LoadBalancerTargetIp extends Model
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @param  string  $ip
     */
    public function __construct(string $ip)
    {
        $this->ip = $ip;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerTargetIp|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->ip);
    }
}
