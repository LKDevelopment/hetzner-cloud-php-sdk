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
use LKDev\HetznerCloud\RequestOpts;

/**
 * Class Prices.
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
     * @param RequestOpts $requestOpts
     * @return \stdClass
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): \stdClass
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('pricing'.$requestOpts->buildQuery());
        if (! HetznerAPIClient::hasError($response)) {
            $this->prices = json_decode((string) $response->getBody())->pricing;

            return $this->prices;
        }
    }
}
