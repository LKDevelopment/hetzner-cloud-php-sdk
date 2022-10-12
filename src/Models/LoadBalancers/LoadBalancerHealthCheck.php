<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\Models\Model;

class LoadBalancerHealthCheck extends Model
{
    /**
     * @var LoadBalancerHealthCheckHttp
     */
    public $http;

    /**
     * @var int
     */
    public $interval;

    /**
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $protocol;

    /**
     * @var int
     */
    public $retries;

    /**
     * @var int
     */
    public $timeout;

    /**
     * @param  LoadBalancerHealthCheckHttp  $http
     * @param  int  $interval
     * @param  int  $port
     * @param  string  $protocol
     * @param  int  $retries
     * @param  int  $timeout
     */
    public function __construct(LoadBalancerHealthCheckHttp $http, int $interval, int $port, string $protocol, int $retries, int $timeout)
    {
        $this->http = $http;
        $this->interval = $interval;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->retries = $retries;
        $this->timeout = $timeout;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerHealthCheck|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self(LoadBalancerHealthCheckHttp::parse($input->http), $input->interval, $input->port, $input->protocol, $input->retries, $input->timeout);
    }
}
