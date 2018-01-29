<?php

namespace LKDev\HetznerCloud;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 *
 */
class APIException extends \Exception
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    public $response;

    /**
     * APIException constructor.
     *
     * @param $response
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(ResponseInterface $response, string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }
}