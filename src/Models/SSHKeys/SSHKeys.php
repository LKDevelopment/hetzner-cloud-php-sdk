<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00.
 */

namespace LKDev\HetznerCloud\Models\SSHKeys;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resources;
use LKDev\HetznerCloud\Models\Meta;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\RequestOpts;
use LKDev\HetznerCloud\Traits\GetFunctionTrait;

class SSHKeys extends Model implements Resources
{
    use GetFunctionTrait;

    /**
     * @var array
     */
    protected $ssh_keys;

    /**
     * Creates a new SSH Key with the given name and public_key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-post
     * @param string $name
     * @param string $publicKey
     * @param array $labels
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function create(
        string $name,
        string $publicKey,
        array $labels = []
    ): ?SSHKey
    {
        $parameters = [
            'name' => $name,
            'public_key' => $publicKey,
        ];
        if (!empty($labels)) {
            $parameters['labels'] = $labels;
        }
        $response = $this->httpClient->post('ssh_keys', [
            'json' => $parameters,
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return SSHKey::parse(json_decode((string)$response->getBody())->ssh_key);
        }

        return null;
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

        return $this->_all($requestOpts);
    }

    /**
     * Returns all ssh key objects.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-get
     * @param RequestOpts $requestOpts
     * @return APIResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function list(RequestOpts $requestOpts = null): ?APIResponse
    {
        if ($requestOpts == null) {
            $requestOpts = new RequestOpts();
        }
        $response = $this->httpClient->get('ssh_keys' . $requestOpts->buildQuery());
        if (!HetznerAPIClient::hasError($response)) {
            $resp = json_decode((string)$response->getBody());

            return APIResponse::create([
                'meta' => Meta::parse($resp->meta),
                $this->_getKeys()['many'] => self::parse($resp->{$this->_getKeys()['many']})->{$this->_getKeys()['many']},
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->ssh_keys = collect($input)->map(function ($sshKey, $key) {
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
    public function getByName(string $name): ?SSHKey
    {
        $sshKeys = $this->list(new SSHKeyRequestOpts($name));

        return (count($sshKeys->ssh_keys) > 0) ? $sshKeys->ssh_keys[0] : null;
    }

    /**
     * @return array
     */
    public function _getKeys(): array
    {
        return ['one' => 'ssh_key', 'many' => 'ssh_keys'];
    }
}
