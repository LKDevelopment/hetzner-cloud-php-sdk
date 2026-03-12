<?php

namespace LKDev\HetznerCloud\Models\Prices;

class Price
{
    /**
     * @var string
     */
    public $net;

    /**
     * @var string
     */
    public $gross;

    /**
     * Price constructor.
     *
     * @param  string  $net
     * @param  string  $gross
     */
    public function __construct(string $net, string $gross)
    {
        $this->net = $net;
        $this->gross = $gross;
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

        return new self($input->net ?? '0', $input->gross ?? '0');
    }
}
