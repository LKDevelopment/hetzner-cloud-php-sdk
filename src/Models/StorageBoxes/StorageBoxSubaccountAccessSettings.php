<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

class StorageBoxSubaccountAccessSettings
{
    public bool $reachable_externally;
    public bool $readonly;
    public bool $samba_enabled;
    public bool $ssh_enabled;
    public bool $webdav_enabled;

    public function __construct(
        bool $reachable_externally = false,
        bool $readonly = false,
        bool $samba_enabled = false,
        bool $ssh_enabled = false,
        bool $webdav_enabled = false,
    ) {
        $this->reachable_externally = $reachable_externally;
        $this->readonly = $readonly;
        $this->samba_enabled = $samba_enabled;
        $this->ssh_enabled = $ssh_enabled;
        $this->webdav_enabled = $webdav_enabled;
    }

    public function toArray(): array
    {
        return [
            'reachable_externally' => $this->reachable_externally,
            'readonly' => $this->readonly,
            'samba_enabled' => $this->samba_enabled,
            'ssh_enabled' => $this->ssh_enabled,
            'webdav_enabled' => $this->webdav_enabled,
        ];
    }

    public static function parse(object $input): ?self
    {
        if ($input == null) {
            return null;
        }

        return new self(
            $input->reachable_externally,
            $input->readonly,
            $input->samba_enabled,
            $input->ssh_enabled,
            $input->webdav_enabled,
        );
    }
}
