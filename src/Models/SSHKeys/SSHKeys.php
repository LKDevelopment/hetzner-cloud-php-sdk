<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00
 */

namespace LKDev\HetznerCloud\Models\SSHKeys;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Volumes\SSHKeyRequestOpts;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

/**
 *
 */
class SSHKeys extends Model implements Resources
{
    use GetFunctionTrait;
    /**
     * @var array
     */
    public $sshKeys;

    /**
     * Creates a new SSH Key with the given name and public_key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-post
     * @param string $name
     * @param string $publicKey
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $name,
        string $publicKey
    ): SSHKey
    {
        $response = $this->httpClient->post('ssh_keys', [
            'json' => [
                'name' => $name,
                'public_key' => $publicKey,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return SSHKey::parse(json_decode((string)$response->getBody())->ssh_key);
        }
    }


    /**
     * Returns all ssh key objects.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $ssh_keys = [];
        $requestOpts->per_page = HetznerAPIClient::MAX_ENTITIES_PER_PAGE;
        for ($i = 1; $i < PHP_INT_MAX; $i++) {
            $_s = $this->list($requestOpts);
            $ssh_keys = array_merge($ssh_keys, $_s);
            if (empty($_s)) {
                break;
            }
        }
        return $ssh_keys;
    }

    /**
     * Returns all ssh key objects.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-get
     * @param RequestOpts $requestOpts
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): array
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('ssh_keys' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->sshKeys;
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->sshKeys = collect($input->ssh_keys)->map(function ($sshKey, $key) {
            return SSHKey::parse($sshKey);
        })->toArray();

        return $this;
    }

    /**
     * @param  $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
    /**
     * Returns a specific ssh key object.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-get-1
     * @param int $sshKeyId
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getById(int $id)
    {
        $response = $this->httpClient->get('ssh_keys/' . $id);
        if (!HetznerAPIClient::hasError($response)) {
            return SSHKey::parse(json_decode((string)$response->getBody())->ssh_key);
        }
    }
    /**
     * Returns a specific ssh key object.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-get-1
     * @param int $sshKeyId
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function getByName(string $name)
    {
        $sshKeys = $this->list(new SSHKeyRequestOpts($name));

        return (count($sshKeys) > 0) ? $sshKeys[0] : null;
    }
}
