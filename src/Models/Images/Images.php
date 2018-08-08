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

class Images extends Model
{
    /**
     * @var array
     */
    public $images;

    /**
     * Returns all image objects.
     *
     * @see https://docs.hetzner.cloud/#resources-images-get
     * @param string|null $name
     * @return array
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function all(string $name = null): array
    {
        $response = $this->httpClient->get('images' . (($name != null) ? '?name=' . $name : ''));
        if (!HetznerAPIClient::hasError($response)) {
            return self::parse(json_decode((string)$response->getBody()))->images;
        }
    }

    /**
     * Returns a specific image object.
     *
     * @see https://docs.hetzner.cloud/#resources-images-get-1
     * @param int $imageId
     * @return \LKDev\HetznerCloud\Models\Images\Image
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function get(int $imageId): Image
    {
        $response = $this->httpClient->get('images/' . $imageId);
        if (!HetznerAPIClient::hasError($response)) {
            return Image::parse(json_decode((string)$response->getBody())->image);
        }
    }

    /**
     * @param  $input
     * @return $this
     */
    public function setAdditionalData($input)
    {
        $this->images = collect($input->images)->map(function ($image, $key) {
            return Image::parse($image);
        })->toArray();

        return $this;
    }

    /**
     * @param $input
     * @return $this|static
     */
    public static function parse($input)
    {
        return (new self())->setAdditionalData($input);
    }
}