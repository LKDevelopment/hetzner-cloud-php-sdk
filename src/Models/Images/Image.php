<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01
 */

namespace LKDev\HetznerCloud\Models\Images;

use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Servers\Server;

/**
 *
 */
class Image extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $imageSize;

    /**
     * @var integer
     */
    public $diskSize;

    /**
     * @var string
     */
    public $created;

    /**
     * @var \LKDev\HetznerCloud\Models\Servers\Server
     */
    public $createdFrom;

    /**
     * @var int
     */
    public $boundTo;

    /**
     * @var string
     */
    public $osFlavor;

    /**
     * @var string
     */
    public $osVersion;

    /**
     * @var bool
     */
    public $rapidDeploy;

    /**
     * Image constructor.
     *
     * @param int $id
     * @param string $type
     * @param string $status
     * @param string $name
     * @param string $description
     * @param float $imageSize
     * @param int $diskSize
     * @param string $created
     * @param \LKDev\HetznerCloud\Models\Servers\Server $createdFrom
     * @param int $boundTo
     * @param string $osFlavor
     * @param string $osVersion
     * @param bool $rapidDeploy
     */
    public function __construct(
        int $id,
        string $type,
        string $status,
        string $name,
        string $description,
        float $imageSize,
        int $diskSize,
        string $created,
        Server $createdFrom,
        int $boundTo,
        string $osFlavor,
        string $osVersion,
        bool $rapidDeploy
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->name = $name;
        $this->description = $description;
        $this->imageSize = $imageSize;
        $this->diskSize = $diskSize;
        $this->created = $created;
        $this->createdFrom = $createdFrom;
        $this->boundTo = $boundTo;
        $this->osFlavor = $osFlavor;
        $this->osVersion = $osVersion;
        $this->rapidDeploy = $rapidDeploy;
        parent::__construct();
    }

    /**
     * Updates the Image. You may change the description or convert a Backup image to a Snapshot Image. Only images of type snapshot and backup can be updated.
     *
     * @see https://docs.hetzner.cloud/#resources-images-put
     * @param string $description
     * @param string $type
     * @return \LKDev\HetznerCloud\Models\Images\Image
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(string $description, string $type): Image
    {
        $response = $this->httpClient->put('images/'.$this->id, [
            'json' => [
                'description' => $description,
                'type' => $type,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->image);
        }
    }

    /**
     * Deletes an Image. Only images of type snapshot and backup can be deleted.
     *
     * @see https://docs.hetzner.cloud/#resources-images-delete
     * @return bool
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('images/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }
    }

    /**
     * @param object $input
     * @return \LKDev\HetznerCloud\Models\Images\Image|static
     */
    public static function parse(object $input)
    {
        return new self($input->id, $input->type, $input->status, $input->name, $input->description, $input->image_size, $input->disk_size, $input->created, Server::parse($input->created_from), $input->bound_to, $input->os_flavor, $input->os_version, $input->rapid_deploy);
    }
}