<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31
 */

namespace Tests\Integration\Servers;

use LKDev\HetznerCloud\Models\Servers\ServerRequestOpts;
use LKDev\HetznerCloud\Models\Servers\Servers;
use Tests\TestCase;

/**
 *
 */
class ServersTest extends TestCase
{
    /**
     * @var Servers
     */
    protected $servers;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->servers = new Servers($this->hetznerApi->getHttpClient());
    }

    /**
     *
     */
    public function testGet()
    {
        $server = $this->servers->get(42);
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
    }

    /**
     *
     */
    public function testGetByName()
    {
        $server = $this->servers->getByName('my-server');
        $this->assertEquals($server->id, 42);
        $this->assertEquals($server->name, 'my-server');
        $this->assertEquals($server->status, 'running');
    }

    /**
     *
     */
    public function testAll()
    {
        $servers = $this->servers->all();

        $this->assertEquals(count($servers), 1);
        $this->assertEquals($servers[0]->id, 42);
        $this->assertEquals($servers[0]->name, 'my-server');
    }

    public function testRequestsObject()
    {
        $c = new ServerRequestOpts("test", "online");

        $this->assertEquals("?name=test&status=online", $c->buildQuery());
    }
}
