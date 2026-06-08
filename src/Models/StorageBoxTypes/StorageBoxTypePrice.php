<?php

namespace LKDev\HetznerCloud\Models\StorageBoxTypes;

use LKDev\HetznerCloud\Models\Prices\Price;

class StorageBoxTypePrice
{
    /**
     * @var string
     */
    public string $location;

    /**
     * @var Price
     */
    public ?Price $price_hourly;

    /**
     * @var Price
     */
    public ?Price $price_monthly;

    /**
     * @var Price|null
     */
    public ?Price $setup_fee;

    /**
     * @param  string  $location
     * @param  Price|null  $priceHourly
     * @param  Price|null  $priceMonthly
     * @param  Price|null  $setupFee
     */
    public function __construct(string $location, ?Price $priceHourly, ?Price $priceMonthly, ?Price $setupFee = null)
    {
        $this->location = $location;
        $this->price_hourly = $priceHourly;
        $this->price_monthly = $priceMonthly;
        $this->setup_fee = $setupFee;
    }

    /**
     * @param  $input
     * @return self|null
     */
    public static function parse($input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self(
            $input->location ?? '',
            Price::parse($input->price_hourly ?? null),
            Price::parse($input->price_monthly ?? null),
            Price::parse($input->setup_fee ?? null)
        );
    }
}
