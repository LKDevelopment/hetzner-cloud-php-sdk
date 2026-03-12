<?php

namespace LKDev\HetznerCloud\Models\Prices;

class ServerTypePrice
{
    /**
     * @var string
     */
    public $location;

    /**
     * @var Price
     */
    public $price_hourly;

    /**
     * @var Price
     */
    public $price_monthly;

    /**
     * ServerTypePrice constructor.
     * @param string $location
     * @param Price $priceHourly
     * @param Price $priceMonthly
     */
    public function __construct(string $location, Price $priceHourly, Price $priceMonthly)
    {
        $this->location = $location;
        $this->price_hourly = $priceHourly;
        $this->price_monthly = $priceMonthly;
    }

    /**
     * @param $input
     * @return self|null
     */
    public static function parse($input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self($input->location, Price::parse($input->price_hourly), Price::parse($input->price_monthly));
    }
}
