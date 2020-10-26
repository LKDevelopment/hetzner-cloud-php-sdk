<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:00.
 */

namespace LKDev\HetznerCloud\Models\Certificates;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;

class Certificate extends Model implements Resource
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
    public $certificate;
    /**
     * @var string
     */
    public $created;
    /**
     * @var string
     */
    public $not_valid_before;
    /**
     * @var string
     */
    public $not_valid_after;
    /**
     * @var array
     */
    public $domain_names;
    /**
     * @var string
     */
    public $fingerprint;
    /**
     * @var \stdClass
     */
    public $used_by;
    /**
     * @var array
     */
    public $labels;

    /**
     * Certificate constructor.
     * @param int $id
     * @param string|null $name
     * @param string|null $certificate
     * @param string|null $created
     * @param string|null $not_valid_before
     * @param string|null $not_valid_after
     * @param array|null $domain_names
     * @param string|null $fingerprint
     * @param array|null $used_by
     * @param array|null $labels
     */
    public function __construct(int $id, string $name = null, string $certificate = null, string $created = null, string $not_valid_before = null, string $not_valid_after = null, array $domain_names = null, string $fingerprint = null, $used_by = null, $labels = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->certificate = $certificate;
        $this->created = $created;
        $this->not_valid_before = $not_valid_before;
        $this->not_valid_after = $not_valid_after;
        $this->domain_names = $domain_names;
        $this->fingerprint = $fingerprint;
        $this->used_by = $used_by;
        $this->labels = $labels;

        parent::__construct();
    }

    /**
     * Update a ssh key.
     *
     * @see https://docs.hetzner.cloud/#resources-certificates-put
     * @param array $data
     * @return \LKDev\HetznerCloud\Models\Certificates\Certificate|null
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?self
    {
        $response = $this->httpClient->put('certificates/'.$this->id, [
            'json' => $data,

        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->certificate);
        }

        return null;
    }

    /**
     * Deletes a SSH key. It cannot be used anymore.
     *
     * @see https://docs.hetzner.cloud/#resources-certificates-delete
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('certificates/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Certificates\Certificate|static
     */
    public static function parse($input)
    {
        return new self($input->id, $input->name, $input->certificate, $input->created, $input->not_valid_before, $input->not_valid_after, $input->domain_names, $input->fingerprint, $input->used_by, $input->labels);
    }

    /**
     * Reload the data of the SSH Key.
     *
     * @return Certificate
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function reload()
    {
        return HetznerAPIClient::$instance->certificates()->get($this->id);
    }
}
