<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 30.05.18
 * Time: 14:13.
 */

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use LKDev\HetznerCloud\HetznerAPIClient;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HetznerAPIClient
     */
    protected $hetznerApi;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    public function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->hetznerApi = new HetznerAPIClient('abcdef', 'http://localhost:4000/v1/');
        $this->hetznerApi->setHttpClient(new Client(['handler' => $this->mockHandler]));
    }

    public function tearDown(): void
    {
        $this->mockHandler->reset();
        parent::tearDown();
    }

    public function assertLastRequestEquals($method, $urlFragment)
    {
        $this->assertEquals($this->mockHandler->getLastRequest()->getMethod(), $method);
        $this->assertEquals('/'.$this->mockHandler->getLastRequest()->getUri()->getPath(), $urlFragment);
    }

    public function assertLastRequestBodyParametersEqual(array $parameters)
    {
        $body = (string) $this->mockHandler->getLastRequest()->getBody();
        $bodyParameters = json_decode($body, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->fail('Invalid json within request: '.json_last_error_msg().':'.$body);
        }
        foreach ($parameters as $parameter => $value) {
            $this->assertArrayHasKey($parameter, $bodyParameters);
            $this->assertEquals($bodyParameters[$parameter], $value);
        }
    }

    public function assertLastRequestBodyIsEmpty()
    {
        $body = (string) $this->mockHandler->getLastRequest()->getBody();
        $this->assertEmpty($body);
    }

    public function assertLastRequestQueryParametersContains(string $key, string $value)
    {
        $query = $this->mockHandler->getLastRequest()->getUri()->getQuery();
        $this->assertStringContainsString(implode('=', [urlencode($key), urlencode($value)]), $query);
    }
}
