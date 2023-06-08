<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.01.18
 * Time: 21:01.
 */

namespace LKDev\HetznerCloud\Models\Images;

use LKDev\HetznerCloud\APIResponse;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Actions\Action;
use LKDev\HetznerCloud\Models\Contracts\Resource;
use LKDev\HetznerCloud\Models\Model;
use LKDev\HetznerCloud\Models\Protection;

class Image extends Model implements Resource
{
    const TYPE_SYSTEM = 'system';
    const TYPE_SNAPSHOT = 'snapshot';
    const TYPE_BACKUP = 'backup';

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
    public $image_size;
    /**
     * @var float
     *
     * @deprecated Use $image_size instead
     */
    public $imageSize;

    /**
     * @var int
     */
    public $disk_size;
    /**
     * @var int
     *
     * @deprecated Use $disk_size instead
     */
    public $diskSize;

    /**
     * @var string
     */
    public $created;

    /**
     * @var \LKDev\HetznerCloud\Models\Servers\Server
     */
    public $created_from;
    /**
     * @var \LKDev\HetznerCloud\Models\Servers\Server
     *
     * @deprecated Use $created_from instead
     */
    public $createdFrom;

    /**
     * @var int
     */
    public $bound_to;
    /**
     * @var int
     *
     * @deprecated Use $bound_to instead
     */
    public $boundTo;

    /**
     * @var string
     */
    public $os_flavor;
    /**
     * @var string
     *
     * @deprecated Use $os_flavor instead
     */
    public $osFlavor;

    /**
     * @var string
     */
    public $os_version;
    /**
     * @var string
     *
     * @deprecated Use $os_version instead
     */
    public $osVersion;

    /**
     * @var bool
     */
    public $rapid_deploy;
    /**
     * @var bool
     *
     * @deprecated Use $rapid_deploy instead
     */
    public $rapidDeploy;

    /**
     * @var array|\LKDev\HetznerCloud\Models\Protection
     */
    public $protection;

    /**
     * @var array
     */
    public $labels;

    /**
     * Image constructor.
     *
     * @param  int  $id
     * @param  string  $type
     * @param  string  $status
     * @param  string  $name
     * @param  string  $description
     * @param  float  $imageSize
     * @param  int  $diskSize
     * @param  string  $created
     * @param  \LKDev\HetznerCloud\Models\Servers\Server  $createdFrom
     * @param  int  $boundTo
     * @param  string  $osFlavor
     * @param  string  $osVersion
     * @param  bool  $rapidDeploy
     * @param  Protection  $protection
     * @param  array  $labels
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
        Protection $protection = null,
        array $labels = []
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->status = $status;
        $this->name = $name;
        $this->description = $description;
        $this->image_size = $imageSize;
        $this->imageSize = $imageSize;
        $this->disk_size = $diskSize;
        $this->diskSize = $diskSize;
        $this->created = $created;
        $this->created_from = $createdFrom;
        $this->createdFrom = $createdFrom;
        $this->bound_to = $boundTo;
        $this->boundTo = $boundTo;
        $this->os_flavor = $osFlavor;
        $this->osFlavor = $osFlavor;
        $this->os_version = $osVersion;
        $this->osVersion = $osVersion;
        $this->rapid_deploy = $rapidDeploy;
        $this->rapidDeploy = $rapidDeploy;
        $this->protection = $protection;
        $this->labels = $labels;
        parent::__construct();
    }

    /**
     * Updates the Image. You may change the description or convert a Backup image to a Snapshot Image. Only images of type snapshot and backup can be updated.
     *
     * @see https://docs.hetzner.cloud/#resources-images-put
     *
     * @param  array  $data
     * @return \LKDev\HetznerCloud\Models\Images\Image
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function update(array $data): ?self
    {
        $response = $this->httpClient->put('images/'.$this->id, [
            'json' => $data,
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string) $response->getBody())->image);
        }

        return null;
    }

    /**
     * Changes the protection configuration of the image. Can only be used on snapshots.
     *
     * @see https://docs.hetzner.cloud/#image-actions-change-image-protection
     *
     * @param  bool  $delete
     * @return APIResponse
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function changeProtection(bool $delete = true): ?APIResponse
    {
        $response = $this->httpClient->post('images/'.$this->id.'/actions/change_protection', [
            'json' => [
                'delete' => $delete,
            ],
        ]);
        if (! HetznerAPIClient::hasError($response)) {
            return APIResponse::create([
                'action' => Action::parse(json_decode((string) $response->getBody())->action),
            ], $response->getHeaders());
        }

        return null;
    }

    /**
     * Deletes an Image. Only images of type snapshot and backup can be deleted.
     *
     * @see https://docs.hetzner.cloud/#resources-images-delete
     *
     * @return bool
     *
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function delete(): bool
    {
        $response = $this->httpClient->delete('images/'.$this->id);
        if (! HetznerAPIClient::hasError($response)) {
            return true;
        }

        return false;
    }

    /**
     * @param  $input
     * @return \LKDev\HetznerCloud\Models\Images\Image|static
     */
    public static function parse($input): ?Image
    {
        if ($input == null) {
            return null;
        }

        return new self($input->id, $input->type, property_exists($input, 'status') ? $input->status : null, $input->name, $input->description, $input->image_size, $input->disk_size, $input->created, $input->created_from, $input->bound_to, $input->os_flavor, $input->os_version, $input->rapid_deploy, Protection::parse($input->protection), get_object_vars($input->labels));
    }

    public function reload()
    {
        return HetznerAPIClient::$instance->images()->get($this->id);
    }
}
