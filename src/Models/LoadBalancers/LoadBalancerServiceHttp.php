<?php

namespace LKDev\HetznerCloud\Models\LoadBalancers;

use LKDev\HetznerCloud\Models\Model;

class LoadBalancerServiceHttp extends Model
{
    /**
     * @var array
     */
    public $certificates;

    /**
     * @var int
     */
    public $cookie_lifetime;

    /**
     * @var string
     */
    public $cookie_name;

    /**
     * @var bool
     */
    public $redirect_http;

    /**
     * @var bool
     */
    public $sticky_sessions;

    /**
     * @param  array  $certificates
     * @param  int  $cookie_lifetime
     * @param  string  $cookie_name
     * @param  bool  $redirect_http
     * @param  bool  $sticky_sessions
     */
    public function __construct(array $certificates, int $cookie_lifetime, string $cookie_name, bool $redirect_http, bool $sticky_sessions)
    {
        $this->certificates = $certificates;
        $this->cookie_lifetime = $cookie_lifetime;
        $this->cookie_name = $cookie_name;
        $this->redirect_http = $redirect_http;
        $this->sticky_sessions = $sticky_sessions;
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancers\LoadBalancerServiceHttp|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->certificates, $input->cookie_lifetime, $input->cookie_name, $input->redirect_http, $input->sticky_essions);
    }
}
