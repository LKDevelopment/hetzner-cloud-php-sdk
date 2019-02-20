<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 20:58
 */

namespace LKDev\HetznerCloud\Models\Servers;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Datacenters\Datacenter;
use LKDev\HetznerCloud\Models\Images\Image;
use LKDev\HetznerCloud\Models\ISOs\ISO;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
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
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $created;

    /**
     * @var array
     */
    public $publicNet;

    /**
     * @var ServerType
     */
    public $serverType;

    /**
     * @var \LKDev\HetznerCloud\Models\Datacenters\Datacenter
     */
    public $datacenter;

    /**
     * @var Image
     */
    public $image;

    /**
     * @var ISO
     */
    public $iso;

    /**
     * @var bool
     */
    public $rescueEnabled;

    /**
     * @var bool
     */
    public $locked;

    /**
     * @var string
     */
    public $backupWindow;

    /**
     * @var int
     */
    public $outgoingTraffic;

    /**
     * @var int
     */
    public $ingoingTraffic;

    /**
     * @var int
     */
    public $includedTraffic;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public $protection;

    /**
     * @var array
     */
    public $labels;

    /**
     * @var array
     */
    public $volumes;

    /**
     *
     *
     * @param int $serverId
     */
    public function __construct(int $serverId)
    {
        $this->id = $serverId;
        parent::__construct();
    }

    /**
     * @param $data
     * @return \LKDev\HetznerCloud\Models\Servers\Server
     */
    public function setAdditionalData($data)
    {
        $this->name = $data->name;
        $this->status = $data->status ?: null;
        $this->publicNet = $data->public_net ?: null;
        $this->serverType = $data->server_type ?: ServerType::parse($data->server_type);
        $this->datacenter = $data->datacenter ?: Datacenter::parse($data->datacenter);
        $this->image = $data->image ?: Image::parse($data->image);
        $this->iso = $data->iso ?: ISO::parse($data->iso);
        $this->rescueEnabled = $data->rescue_enabled ?: null;
        $this->locked = $data->locked ?: null;
        $this->backupWindow = $data->backup_window ?: null;
        $this->outgoingTraffic = $data->outgoing_traffic ?: null;
        $this->ingoingTraffic = $data->ingoing_traffic ?: null;
        $this->includedTraffic = $data->included_traffic ?: null;
        $this->volumes = property_exists($data, 'volumes') ? $data->volumes : [];
        $this->protection = $data->protection ?: Protection::parse($data->protection);
        $this->labels = $data->labels;
        return $this;
    }

    /**
     * Reload the data of the server
     *
     * @return Server
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get()
    {
        $servers = new Servers();

        return $servers->get($this->id);
    }

    /**
     * Starts a server by turning its power on.
     *
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function powerOn(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/poweron'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Reboots a server gracefully by sending an ACPI request. The server operating system must support ACPI and react to the request, otherwise the server will not reboot.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-1
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function softReboot(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reboot'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Cuts power to a server and starts it again. This forcefully stops it without giving the server operating system time to gracefully stop. This may lead to data loss, it’s equivalent to pulling the power cord and plugging it in again. Reset should only be used when reboot does not work.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-2
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reset(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reset'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Shuts down a server gracefully by sending an ACPI shutdown request. The server operating system must support ACPI and react to the request, otherwise the server will not shut down.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-3
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function shutdown(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/shutdown'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Cuts power to the server. This forcefully stops it without giving the server operating system time to gracefully stop. May lead to data loss, equivalent to pulling the power cord. Power off should only be used when shutdown does not work.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-4
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function powerOff(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/poweroff'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Resets the root password. Only works for Linux systems that are running the qemu guest agent. Server must be powered on (state on) in order for this operation to succeed.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-5
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function resetRootPassword(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/reset_password'));
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'root_password' => $payload->root_password
            ], $response->getHeaders());
        }
    }

    /**
     * Enable the Hetzner Rescue System for this server. The next time a Server with enabled rescue mode boots it will start a special minimal Linux distribution designed for repair and reinstall.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-6
     * @param string $type
     * @param array $ssh_keys
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function enableRescue($type = 'linux64', $ssh_keys = []): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/enable_rescue'), [
            'json' => [
                'type' => $type,
                'ssh_keys' => $ssh_keys,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'root_password' => $payload->root_password
            ], $response->getHeaders());
        }
    }

    /**
     * Disables the Hetzner Rescue System for a server. This makes a server start from its disks on next reboot.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-7
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function disableRescue(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/disable_rescue'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Creates an image (snapshot) from a server by copying the contents of its disks. This creates a snapshot of the current state of the disk and copies it into an image. If the server is currently running you must make sure that its disk content is consistent. Otherwise, the created image may not be readable.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-8
     * @param string $description
     * @param string $type
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function createImage(string $description = '', string $type = 'snapshot'): APIResponse
    {

        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/create_image'), [
            'json' => [
                'description' => $description,
                'type' => $type,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'image' => Image::parse($payload->image)
            ], $response->getHeaders());
        }
    }

    /**
     * Rebuilds a server overwriting its disk with the content of an image, thereby destroying all data on the target server
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-9
     * @param \LKDev\HetznerCloud\Models\Images\Image $image
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function rebuildFromImage(Image $image): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/rebuild'), [
            'json' => [
                'image' => $image->name,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the type (Cores, RAM and disk sizes) of a server.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-10
     * @param \LKDev\HetznerCloud\Models\Servers\Types\ServerType $serverType
     * @param bool $upgradeDisk
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeType(ServerType $serverType, bool $upgradeDisk = false): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/change_type'), [
            'json' => [
                'server_type' => $serverType->name,
                'upgrade_disk' => $upgradeDisk,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Enables and configures the automatic daily backup option for the server. Enabling automatic backups will increase the price of the server by 20%
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-11
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function enableBackups(string $backupWindow = null): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/enable_backup'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Disables the automatic backup option and deletes all existing Backups for a Server.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-12
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function disableBackups(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/disable_backup'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Attaches an ISO to a server. The Server will immediately see it as a new disk. An already attached ISO will automatically be detached before the new ISO is attached.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-13
     * @param \LKDev\HetznerCloud\Models\ISOs\ISO $iso
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function attachISO(ISO $iso): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/attach_iso'), [
            'json' => [
                'iso' => $iso->name,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Detaches an ISO from a server. In case no ISO image is attached to the server, the status of the returned action is immediately set to success.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-14
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function detachISO(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/detach_iso'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the hostname that will appear when getting the hostname belonging to the primary IPs (ipv4 and ipv6) of this server.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-15
     * @param string $ip
     * @param string $dnsPtr
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeReverseDNS(string $ip, string $dnsPtr): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/change_dns_ptr'), [
            'json' => [
                'ip' => $ip,
                'dns_ptr' => $dnsPtr,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Get Metrics for specified server.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-get-2
     * @param string $type
     * @param string $start
     * @param string $end
     * @param int|null $step
     */
    public function metrics(string $type, string $start, string $end, int $step = null)
    {
        // ToDo
        $this->httpClient->get($this->replaceServerIdInUri('servers/{id}/metrics?') . http_build_query(compact('type', 'start', 'end', 'step')));
    }

    /**
     * Deletes a server. This immediately removes the server from your account, and it is no longer accessible.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-delete
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): APIResponse
    {
        $response = $this->httpClient->delete($this->replaceServerIdInUri('servers/{id}'));
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * Update a server with new meta data.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-put
     * @param array $data
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data)
    {
        $response = $this->httpClient->put($this->replaceServerIdInUri('servers/{id}'), [
            'json' => $data,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'server' => Server::parse(json_decode((string)$response->getBody())->server)
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the name of a server.
     *
     * @see https://docs.hetzner.cloud/#resources-servers-put
     * @param string $name
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     * @deprecated 1.1.0
     */
    public function changeName(string $name): APIResponse
    {
        return $this->update(['name' => $name]);
    }

    /**
     * Requests credentials for remote access via vnc over websocket to keyboard, monitor, and mouse for a server
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-16
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function requestConsole(): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/request_console'));
        if (!HetznerAPIClient::hasError($response)) {
            $payload = json_decode((string)$response->getBody());
            return APIResponse::create([
                'action' => Action::parse($payload->action),
                'wss_url' => $payload->wss_url,
                'password' => $payload->password
            ], $response->getHeaders());
        }
    }

    /**
     * Changes the protection configuration of the server.
     *
     * @see https://docs.hetzner.cloud/#resources-server-actions-post-16
     * @param bool $delete
     * @param bool $rebuild
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true, bool $rebuild = true): APIResponse
    {
        $response = $this->httpClient->post($this->replaceServerIdInUri('servers/{id}/actions/change_protection'), [
            'json' => [
                'delete' => $delete,
                'rebuild' => $rebuild,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ], $response->getHeaders());
        }
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function replaceServerIdInUri(string $uri): string
    {
        return str_replace('{id}', $this->id, $uri);
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Servers\Server|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return (new self($input->id))->setAdditionalData($input);
    }
}
