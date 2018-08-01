<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:02
 */

namespace LKDev\HetznerCloud\Models\Prices;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

/**
 * Class Prices
 * @package LKDev\HetznerCloud\Models\Prices
 */
class Prices extends Model
{
    /**
     * @var \stdClass
     */
    public $prices;

    /**
     * Returns all pricing information.
     *
     * @see https://docs.hetzner.cloud/#pricing-get-all-prices
     * @return \stdClass
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(): \stdClass
    {
        $response = $this->httpClient->get('pricing');
        if (!HetznerAPIClient::hasError($response)) {
            $this->prices = json_decode((string)$response->getBody());
            return $this->prices;
        }
    }
}