<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\Models\Model;

class LoadBalancerHealthCheckHttp extends Model
{
    /**
     * @var string|null
     */
    public $domain;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $response;

    /**
     * @var array
     */
    public $status_codes;

    /**
     * @var bool
     */
    public $tls;

    /**
     * @param string|null $domain
     * @param string $path
     * @param string $response
     * @param array $status_codes
     * @param bool $tls
     */
    public function __construct(?string $domain, string $path, string $response, array $status_codes, bool $tls)
    {
        $this->domain = $domain;
        $this->path = $path;
        $this->response = $response;
        $this->status_codes = $status_codes;
        $this->tls = $tls;
        parent::__construct(null);
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerHealthCheckHttp|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->domain, $input->path, $input->response, $input->status_codes, $input->tls);
    }
}
