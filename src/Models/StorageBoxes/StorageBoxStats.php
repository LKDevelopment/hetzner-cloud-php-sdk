<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

class StorageBoxStats
{
    /**
     * @var int
     */
    public int $size;

    /**
     * @var int
     */
    public int $size_data;

    /**
     * @var int
     */
    public int $size_snapshots;

    /**
     * @param  int  $size
     * @param  int  $size_data
     * @param  int  $size_snapshots
     */
    public function __construct(int $size, int $size_data, int $size_snapshots)
    {
        $this->size = $size;
        $this->size_data = $size_data;
        $this->size_snapshots = $size_snapshots;
    }

    /**
     * @param  object     $input
     * @return self|null
     */
    public static function parse(object $input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self(
            $input->size,
            $input->size_data,
            $input->size_snapshots
        );
    }
}
