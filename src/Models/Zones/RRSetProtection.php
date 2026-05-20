<?php

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 01.04.18
 * Time: 19:02.
 */

namespace LKDev\HetznerCloud\Models\Zones;

// This is a read only model, that does not have any logic. Just a stupid dataholder.
use LKDev\HetznerCloud\Models\Model;

class RRSetProtection extends Model
{
    /**
     * @var bool
     */
    public $change;

    /**
     * Protection constructor.
     *
     * @param  bool  $change
     */
    public function __construct(bool $delete)
    {
        $this->change = $delete;
        // Force getting the default http client
        parent::__construct(null);
    }

    /**
     * @param  array  $input
     * @return ?RRSetProtection
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }
        if (! is_array($input)) {
            $input = get_object_vars($input);
        }

        return new self($input['change'] ?? false);
    }
}
