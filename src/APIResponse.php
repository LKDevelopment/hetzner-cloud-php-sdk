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
class APIResponse
{

    /**
     * @var array
     */
    protected $header = [];
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
     * @param array $header
     */
    public function setHeader(array $header)
    {
        $this->header = $header;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $response
     * @param array $header
     * @return APIResponse
     */
    public static function create(array $response, array $header = [])
    {
        $apiResponse = new APIResponse();
        $apiResponse->setResponse($response);
        $apiResponse->setHeader($header);
        return $apiResponse;
    }
}