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
     * @var \DateTimeInterface
     */
    public $created;

    /**
     * SSHKey constructor.
     *
     * @param  int  $id
     * @param  string  $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;

        parent::__construct();

        // @deprecated code
        if (func_num_args() > 2) {
            $this->fingerprint = func_get_arg(2);
            if (func_get_arg(3)) {
                $this->public_key = func_get_arg(3);
            }
            if (func_get_arg(4)) {
                $this->labels = func_get_arg(4);
            }
        }
    }

    /**
     * Update a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-put
     *
     * @param  array  $data
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?static
    {
        $response = $this->httpClient->put('ssh_keys/'.$this->id, [
            'json' => $data,

        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return static::parse(json_decode((string) $response->getBody())->ssh_key);
        }

        return null;
    }

    /**
     * Changes the name of a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-put
     *
     * @param  string  $newName
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey|null
     *
     * @throws \LKDev\HetznerCloud\APIException
     *
     * @deprecated 1.2.0
     */
    public function changeName(string $newName): ?static
    {
        return $this->update(['name' => $newName]);
    }

    /**
     * Deletes a SSH key. It cannot be used anymore.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-delete
     *
     * @return bool
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('ssh_keys/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey|static
     */
    public static function parse($input)
    {
        return (new static($input->id, $input->name))->setAdditionalData($input);
    }

    /**
     * Reload the data of the SSH Key.
     *
     * @return SSHKey
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->sshKeys()->get($this->id);
    }

    public function setAdditionalData($data): static
    {
        $this->fingerprint = $data->fingerprint;
        $this->public_key = $data->public_key;
        $this->labels = get_object_vars($data->labels);
        $this->created = new \DateTime($data->created);

        return $this;
    }
}
