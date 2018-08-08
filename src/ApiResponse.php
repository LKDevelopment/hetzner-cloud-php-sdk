<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 08.08.18
 * Time: 14:06
 */

namespace LKDev\HetznerCloud;


use LKDev\HetznerCloud\Models\Model;

/**
 * Class ApiResponse
 * @package LKDev\HetznerCloud
 */
class ApiResponse
{
    /**
     * @var array
     */
    protected $response = [];

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @param string|null $resource
     * @return Model|string|boolean
     */
    public function getResponsePart(string $resource = null)
    {
        return (array_key_exists($resource, $this->response)) ? $this->response[$resource] : false;
    }

    /**
     * @param array $response
     */
    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * @param array $response
     * @return ApiResponse
     */
    public static function create(array $response)
    {
        $apiResponse = new ApiResponse();
        $apiResponse->setResponse($response);
        return $apiResponse;
    }
}