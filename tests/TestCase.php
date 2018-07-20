<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 30.05.18
 * Time: 14:13
 */

namespace Tests;

use GuzzleHttp\Client;
use LKDev\HetznerCloud\HetznerAPIClient;

/**
 *
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var
     */
    protected $hetznerApi;

    /**
     *
     */
    public function setUp()
    {
        $this->hetznerApi = new HetznerAPIClient('abcdef', 'http://localhost:8080');
    }
}