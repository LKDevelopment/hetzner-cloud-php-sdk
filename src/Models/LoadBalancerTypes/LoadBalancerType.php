<?php

namespace LKDev\HetznerCloud\Models\LoadBalancerTypes;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Prices\Prices;

class LoadBalancerType extends Model implements Resource
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $deprecated;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $max_assigned_certificates;

    /**
     * @var int
     */
    public $max_connections;

    /**
     * @var int
     */
    public $max_services;

    /**
     * @var int
     */
    public $max_targets;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Prices\Prices
     */
    public $prices;

    /**
     * @param  int  $id
     * @param  string  $name
     * @param  string  $deprecated
     * @param  string  $description
     * @param  int  $max_assigned_certificates
     * @param  int  $max_connections
     * @param  int  $max_services
     * @param  int  $max_targets
     * @param  array|\LKDev\HetznerCloud\Models\Prices\Prices  $prices
     */
    public function __construct(int $id, string $name, string $deprecated, string $description, int $max_assigned_certificates, int $max_connections, int $max_services, int $max_targets, $prices)
    {
        $this->id = $id;
        $this->name = $name;
        $this->deprecated = $deprecated;
        $this->description = $description;
        $this->max_assigned_certificates = $max_assigned_certificates;
        $this->max_connections = $max_connections;
        $this->max_services = $max_services;
        $this->max_targets = $max_targets;
        $this->prices = $prices; //
        parent::__construct();
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->id, $input->name, $input->deprecated, $input->description, $input->max_assigned_certificates, $input->max_connections, $input->max_services, $input->max_targets, Prices::parse($input->prices));
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->loadBalancerTypes()->get($this->id);
    }

    public function delete()
    {
        throw new \BadMethodCallException('delete on load balancer type is not possible');
    }

    public function update(array $data)
    {
        throw new \BadMethodCallException('update on load balancer type is not possible');
    }
}
