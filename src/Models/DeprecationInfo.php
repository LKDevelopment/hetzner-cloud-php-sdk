<?php

namespace LKDev\HetznerCloud\Models;

class DeprecationInfo extends Model
{
    /**
     * @var string
     */
    public $announced;

    /**
     * @var string
     */
    public $unavailableAfter;

    public function __construct(string $announced, string $unavailableAfter)
    {
        $this->announced = $announced;
        $this->unavailableAfter = $unavailableAfter;
        parent::__construct();
    }

    /**
     * @param  $input
     * @return self|null
     */
    public static function parse($input): ?self
    {
        if ($input === null) {
            return null;
        }

        return new self($input->announced, $input->unavailable_after);
    }
}
