<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01
 */

namespace LKDev\HetznerCloud\Models\Images;

use LKDev\HetznerCloud\ApiResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;
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
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public $protection;

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
     * @param Protection $protection
     */
    public function __construct(
        int $id,
        string $type = null,
        string $status = null,
        string $name = null,
        string $description = null,
        float $imageSize = null,
        int $diskSize = null,
        string $created = null,
        $createdFrom = null,
        int $boundTo = null,
        string $osFlavor = null,
        string $osVersion = null,
        bool $rapidDeploy = null,
        Protection $protection = null
    )
    {
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
        $this->protection = $protection;
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
    public function update(string $description = null, string $type = null): Image
    {
        $response = $this->httpClient->put('images/' . $this->id, [
            'json' => [
                'description' => $description == null ? $this->description : $type,
                'type' => $type == null ? $this->type : $type,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody())->image);
        }
    }

    /**
     * Changes the protection configuration of the image. Can only be used on snapshots.
     *
     * @see https://docs.hetzner.cloud/#resources-image-actions-post
     * @param bool $delete
     * @return ApiResponse
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): ApiResponse
    {
        $response = $this->httpClient->post('images/' . $this->id . '/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (!HetznerAPIClient::hasError($response)) {
            return ApiResponse::create([
                'action' => Action::parse(json_decode((string)$response->getBody())->action)
            ]);
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
        $response = $this->httpClient->delete('images/' . $this->id);
        if (!HetznerAPIClient::hasError($response)) {
            return true;
        }
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Images\Image|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->type, (property_exists($input, 'status') ? $input->status : null), $input->name, $input->description, $input->image_size, $input->disk_size, $input->created, $input->created_from, $input->bound_to, $input->os_flavor, $input->os_version, $input->rapid_deploy, Protection::parse($input->protection));
    }
}