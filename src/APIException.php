<?php

namespace LKDev\HetznerCloud;

class APIException extends \Exception
{
    /**
     * @var APIResponse
     */
    protected $response;

    /**
     * APIException constructor.
     *
     * @param $response
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(APIResponse $response, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return APIResponse
     */
    public function getApiResponse()
    {
        return $this->response;
    }
}
