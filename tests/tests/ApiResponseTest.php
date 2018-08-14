<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 08.08.18
 * Time: 14:35
 */

namespace Tests\tests;

use LKDev\HetznerCloud\APIResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{

    public function testSetResponse()
    {
        $apiResponse = new APIResponse();
        $apiResponse->setResponse(['response1' => '12345', 'response2' => 1234, 'response3' => ['abc', 123, [456, 'efg']]]);
        $this->assertCount(3, $apiResponse->getResponse());
    }

    public function testCreate()
    {
        $apiResponse = APIResponse::create(['response1' => '12345', 'response2' => 1234, 'response3' => ['abc', 123, [456, 'efg']]]);
        $this->assertCount(3, $apiResponse->getResponse());
    }

    public function testGetResponsePart()
    {
        $apiResponse = new APIResponse();
        $apiResponse->setResponse(['response1' => '12345', 'response2' => 1234, 'response3' => ['abc', 123, [456, 'efg']]]);
        $this->assertEquals('12345', $apiResponse->getResponsePart('response1'));
        $this->assertEquals(1234, $apiResponse->getResponsePart('response2'));
        $this->assertFalse($apiResponse->getResponsePart('not_there'));
    }
}
