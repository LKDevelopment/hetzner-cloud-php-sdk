<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00
 */

namespace LKDev\HetznerCloud\Models\SSHKeys;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;

/**
 *
 */
class SSHKey extends Model
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
    public $publicKey;

    /**
     * SSHKey constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $fingerprint
     * @param string $publicKey
     */
    public function __construct(int $id, string $name, string $fingerprint, string $publicKey)
    {
        $this->id = $id;
        $this->name = $name;
        $this->fingerprint = $fingerprint;
        $this->publicKey = $publicKey;
        parent::__construct();
    }

    /**
     * Changes the name of a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-put
     * @param string $newName
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeName(string $newName): SSHKey{
        $response = $this->httpClient->put('ssh_keys/'.$this->id,[
            'json' => [
                'name' => $newName
            ]
        ]);
        if(!HetznerAPIClient::hasError($response)){
            return self::parse(json_decode((string) $response->getBody())->ssh_key);
        }
    }
    /**
     * Deletes a SSH key. It cannot be used anymore.
     *
     * @see https://docs.hetzner.cloud/#resources-ssh-keys-delete
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete():bool
    {
        $response = $this->httpClient->delete('ssh_keys/'.$this->id);
        if(!HetznerAPIClient::hasError($response)){
            return true;
        }
    }

    /**
     * @param object $input
     * @return \LKDev\HetznerCloud\Models\SSHKeys\SSHKey|static
     */
    public static function parse(object $input)
    {
        return new self($input->id, $input->name, $input->fingerprint, $input->public_key);
    }
}