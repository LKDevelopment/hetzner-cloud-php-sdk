<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:58
 */

namespace LKDev\HetznerCloud\Models\Servers;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\ISOs\ISO;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Types\ServerType;

/**
 *
 */
class Server extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     *
     *
     * @param int $serverId
     * @param HetznerAPIClient $hetznerAPIClient
     * @param \LKDev\HetznerCloud\Clients\GuzzleClient $httpClient
     */
    public function __construct(
        int $serverId,
        HetznerAPIClient $hetznerAPIClient,
        $httpClient = null
    ) {
        $this->id = $serverId;
        parent::__construct($hetznerAPIClient, $httpClient);
    }

    /**
     * @return bool
     */
    public function powerOn(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/poweron'));
    }

    /**
     * @return bool
     */
    public function softReboot(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reboot'));
    }

    /**
     * @return bool
     */
    public function reset(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reset'));
    }

    /**
     * @return bool
     */
    public function shutdown(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/shutdown'));
    }

    /**
     * @return bool
     */
    public function powerOff(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/powerOff'));
    }

    /**
     * @return string
     */
    public function resetRootPassword(): string
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reset_password'));
    }

    /**
     * @param string $type
     * @param array $ssh_keys
     * @return bool
     */
    public function enableRescue($type = 'linux64', $ssh_keys = []): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/enable_rescue'), [
            'form_params' => [
                'type' => $type,
                'ssh_keys' => $ssh_keys,
            ],
        ]);
    }

    /**
     * @return bool
     */
    public function disableRescue(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/disable_rescue'));
    }

    /**
     * @param string $description
     * @param string $type
     * @return \LKDev\HetznerCloud\Models\Images\Image
     */
    public function createImage(string $description = '', string $type = 'snapshot'): Image
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/create_image'), [
            'form_params' => [
                'description' => $description,
                'type' => $type,
            ],
        ]);
    }

    /**
     * @param \LKDev\HetznerCloud\Models\Images\Image $image
     * @return bool
     */
    public function rebuildFromImage(Image $image): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/rebuild'), [
            'form_params' => [
                'image' => $image->id,
            ],
        ]);
    }

    /**
     * @param \LKDev\HetznerCloud\Models\Servers\Types\ServerType $serverType
     * @param bool $upgradeDisk
     * @return bool
     */
    public function changeType(ServerType $serverType, bool $upgradeDisk = false): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/change_type'), [
            'form_params' => [
                'server_type' => $serverType->id,
                'upgrade_disk' => $upgradeDisk,
            ],
        ]);
    }

    /**
     * @param string|null $backupWindow
     * @return bool
     */
    public function enableBackups(string $backupWindow = null): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/enable_backup'), [
            'form_params' => [
                'backup_window' => $backupWindow,
            ],
        ]);
    }

    /**
     * @return bool
     */
    public function disableBackups(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/disable_backup'));
    }

    /**
     * @param \LKDev\HetznerCloud\Models\ISOs\ISO $iso
     */
    public function attachISO(ISO $iso)
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/attach_iso'), [
            'form_params' => [
                'iso' => $iso->id,
            ],
        ]);
    }

    /**
     * @return bool
     */
    public function dettachISO(): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/detach_iso'));
    }

    /**
     * @param string $ip
     * @param string $dnsPtr
     * @return bool
     */
    public function changeReverseDNS(string $ip, string $dnsPtr): bool
    {
        $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/change_dns_ptr'), [
            'form_params' => [
                'ip' => $ip,
                'dns_ptr' => $dnsPtr,
            ],
        ]);
    }

    public function metrics(string $type, string $start, string $end, int $step = null)
    {
        $this->httpClient->get($this->replaceServerIdInUri('servers/{id}/metrics?').http_build_query(compact('type', 'start', 'end', 'step')));
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $this->httpClient->delete($this->replaceServerIdInUri('servers/{id}'));
    }

    /**
     * @param string $name
     */
    public function changeName(string $name)
    {
        $this->httpClient->put($this->replaceServerIdInUri('servers/{id}'), [
            'form_params' => [
                'name' => $name,
            ],
        ]);
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function replaceServerIdInUri(string $uri): string
    {
        return str_replace('{id}', $this->id, $uri);
    }
}