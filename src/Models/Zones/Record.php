<?php

namespace LKDev\HetznerCloud\Models\Zones;

class Record
{
    public string $value;
    public string $comment;

    public function __construct(string $value, string $comment)
    {
        $this->value = $value;
        $this->comment = $comment;
    }
}
