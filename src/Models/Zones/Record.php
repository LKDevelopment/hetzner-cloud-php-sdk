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

    public static function parse($input): self
    {
        return new Record($input->value, $input->comment);
    }
}
