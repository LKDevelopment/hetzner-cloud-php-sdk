<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00.
 */

namespace LKDev\HetznerCloud\Models\SSHKeys;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class SSHKey extends Model implements Resource
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
    public $fingerprint;

    /**
     * @var string
     */
    public $public_key;

    /**
     * @var array
     */
    public $labels;

    /**
     * SSHKey constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $fingerprint
     * @param string $publicKey
     * @param array $labels
     */
    public function __construct(int $id, string $name, string $fingerprint, string $publicKey, array $labels = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->fingerprint = $fingerprint;
        $this->public_key = $publicKey;
        $this->labels = $labels;
        parent::__construct();
    }

    /**
     * Update a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-put
     * @param array $data
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): self
    {
        $response = $this->httpClient->put('ssh_keys/'.$this->id, [
            'json' => $data,

        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->ssh_key);
        }
    }

    /**
     * Changes the name of a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-put
     * @param string $newName
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     * @deprecated 1.2.0
     */
    public function changeName(string $newName): self
    {
        return $this->update(['name' => $newName]);
    }

    /**
     * Deletes a SSH key. It cannot be used anymore.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-delete
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('ssh_keys/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey|static
     */
    public static function parse($input)
    {
        return new self($input->id, $input->name, $input->fingerprint, $input->public_key, get_object_vars($input->labels));
    }

    /**
     * Reload the data of the SSH Key.
     *
     * @return SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->sshKeys()->get($this->id);
    }
}
