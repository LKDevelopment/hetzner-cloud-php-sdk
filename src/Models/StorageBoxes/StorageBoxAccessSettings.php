<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

class StorageBoxAccessSettings
{
    /**
     * @var bool
     */
    public bool $reachable_externally;

    /**
     * @var bool
     */
    public bool $samba_enabled;

    /**
     * @var bool
     */
    public bool $ssh_enabled;

    /**
     * @var bool
     */
    public bool $webdav_enabled;

    /**
     * @var bool
     */
    public bool $zfs_enabled;

    /**
     * @param  bool  $reachable_externally
     * @param  bool  $samba_enabled
     * @param  bool  $ssh_enabled
     * @param  bool  $webdav_enabled
     * @param  bool  $zfs_enabled
     */
    public function __construct(
        bool $reachable_externally = false,
        bool $samba_enabled = false,
        bool $ssh_enabled = false,
        bool $webdav_enabled = false,
        bool $zfs_enabled = false,
    ) {
        $this->reachable_externally = $reachable_externally;
        $this->samba_enabled = $samba_enabled;
        $this->ssh_enabled = $ssh_enabled;
        $this->webdav_enabled = $webdav_enabled;
        $this->zfs_enabled = $zfs_enabled;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'reachable_externally' => $this->reachable_externally,
            'samba_enabled' => $this->samba_enabled,
            'ssh_enabled' => $this->ssh_enabled,
            'webdav_enabled' => $this->webdav_enabled,
            'zfs_enabled' => $this->zfs_enabled,
        ];
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
            $input->reachable_externally,
            $input->samba_enabled,
            $input->ssh_enabled,
            $input->webdav_enabled,
            $input->zfs_enabled
        );
    }
}
