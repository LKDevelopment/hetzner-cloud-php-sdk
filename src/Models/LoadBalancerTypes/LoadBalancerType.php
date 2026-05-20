<?php

namespace LKDev\HetznerCloud\Models\LoadBalancerTypes;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\Prices\ServerTypePrice;

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
     * @var string|null
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
     * @var array
     */
    public $price;

    /**
     * @param  int  $id
     * @param  string  $name
     * @param  string|null  $deprecated
     * @param  string  $description
     * @param  int  $max_assigned_certificates
     * @param  int  $max_connections
     * @param  int  $max_services
     * @param  int  $max_targets
     * @param  array|\LKDev\HetznerCloud\Models\Prices\Prices  $prices
     * @param  array  $price
     */
    public function __construct(int $id, string $name, ?string $deprecated, string $description, int $max_assigned_certificates, int $max_connections, int $max_services, int $max_targets, $prices, $price = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->deprecated = $deprecated;
        $this->description = $description;
        $this->max_assigned_certificates = $max_assigned_certificates;
        $this->max_connections = $max_connections;
        $this->max_services = $max_services;
        $this->max_targets = $max_targets;
        $this->prices = $prices;
        $this->price = $price;
        parent::__construct();
    }

    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return new self(
            $input->id,
            $input->name,
            $input->deprecated ?? null,
            $input->description ?? '',
            $input->max_assigned_certificates ?? 0,
            $input->max_connections ?? 0,
            $input->max_services ?? 0,
            $input->max_targets ?? 0,
            Prices::parse($input->prices),
            property_exists($input, 'price') ? ServerTypePrice::parse($input->price) : null
        );
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
