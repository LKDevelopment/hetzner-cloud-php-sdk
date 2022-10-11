<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\Models\Model;

class LoadBalancerAlgorithm extends Model
{
    /**
     * @var array
     */
    public $str;

    /**
     * @param array $str
     */
    public function __construct(array $str)
    {
        $this->str = $str;
        parent::__construct();
    }


    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerAlgorithm|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->str);
    }
}
