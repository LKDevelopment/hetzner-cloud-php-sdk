<?php

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:02.
 */

namespace LKDev\HetznerCloud\Models\Prices;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use LKDev\HetznerCloud\Models\LoadBalancerTypes\LoadBalancerType;
use LKDev\HetznerCloud\RequestOpts;

/**
 * Class Prices.
 */
class Prices extends Model
{
    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $vat_rate;

    /**
     * @var Price
     */
    public $image;

    /**
     * @var Price
     */
    public $floating_ip;

    /**
     * @var Price
     */
    public $traffic;

    /**
     * @var string
     */
    public $server_backup;

    /**
     * @var Price
     */
    public $volume;

    /**
     * @var array
     */
    public $server_types;

    /**
     * @var array
     */
    public $load_balancer_types;

    /**
     * @param  RequestOpts  $requestOpts
     * @return Prices|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(?RequestOpts $requestOpts = null): ?self
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('pricing'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string) $response->getBody())->pricing;

            return $this->setAdditionalData($payload);
        }

        return null;
    }

    /**
     * @param $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->currency = $input->currency;
        $this->vat_rate = $input->vat_rate;
        $this->image = Price::parse($input->image->price_per_gb_month);
        $this->floating_ip = Price::parse($input->floating_ip->price_monthly);
        $this->traffic = Price::parse($input->traffic->price_per_tb);
        $this->server_backup = $input->server_backup->percentage;
        $this->volume = Price::parse($input->volume->price_per_gb_month);
        if (property_exists($input, 'server_types')) {
            $this->server_types = collect($input->server_types)->map(function ($serverType) {
                return ServerType::parse($serverType);
            })->toArray();
        }
        if (property_exists($input, 'load_balancer_types')) {
            $this->load_balancer_types = collect($input->load_balancer_types)->map(function ($loadBalancerType) {
                return LoadBalancerType::parse($loadBalancerType);
            })->toArray();
        }

        return $this;
    }

    /**
     * @param $input
     * @return array
     */
    public static function parse($input)
    {
        if ($input == null) {
            return [];
        }

        return collect($input)->map(function ($price) {
            return ServerTypePrice::parse($price);
        })->toArray();
    }
}
