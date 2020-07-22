<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration;

use LKDev\HetznerCloud\Models\Prices\Prices;
use Tests\TestCase;

class PricingTest extends TestCase
{
    /**
     * @var \LKDev\HetznerCloud\Models\Prices\Prices
     */
    protected $prices;

    public function setUp(): void
    {
        parent::setUp();
        $this->prices = new Prices($this->hetznerApi->getHttpClient());
    }

    public function testAll()
    {
        $prices = $this->prices->all();
        $this->assertEquals('EUR', $prices->currency);
        $this->assertEquals('19.000000', $prices->vat_rate);
        $this->assertEquals('1.0000000000', $prices->image->price_per_gb_month->net);
        $this->assertIsArray($prices->server_types);
    }
}
