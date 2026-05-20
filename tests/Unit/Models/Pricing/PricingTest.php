<?php

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace LKDev\Tests\Unit\Pricing;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Prices\Price;
use LKDev\HetznerCloud\Models\Prices\Prices;
use LKDev\HetznerCloud\Models\Prices\ServerTypePrice;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;
use LKDev\Tests\TestCase;

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
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/pricing.json')));
        $prices = $this->prices->all();
        $this->assertEquals('EUR', $prices->currency);
        $this->assertEquals('19.000000', $prices->vat_rate);
        $this->assertInstanceOf(Price::class, $prices->image);
        $this->assertEquals('1.0000000000', $prices->image->net);
        $this->assertEquals('1.1900000000000000', $prices->image->gross);

        $this->assertInstanceOf(Price::class, $prices->floating_ip);
        $this->assertInstanceOf(Price::class, $prices->traffic);
        $this->assertEquals('20.0000000000', $prices->server_backup);
        $this->assertInstanceOf(Price::class, $prices->volume);

        $this->assertIsArray($prices->server_types);
        $this->assertInstanceOf(ServerType::class, $prices->server_types[0]);
        $this->assertIsArray($prices->server_types[0]->prices);
        $this->assertInstanceOf(ServerTypePrice::class, $prices->server_types[0]->prices[0]);

        $this->assertIsArray($prices->load_balancer_types);
        $this->assertLastRequestEquals('GET', '/pricing');
    }
}
