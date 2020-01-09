<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration;

use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;
use Tests\TestCase;

class SSHKeysTest extends TestCase
{
    /**
     * @var  \LKDev\HetznerCloud\Models\SSHKeys\SSHKeys
     */
    protected $ssh_keys;

    public function setUp()
    {
        parent::setUp();
        $this->ssh_keys = new SSHKeys($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $server_type = $this->ssh_keys->get(1);
        $this->assertEquals($server_type->id, 1);
        $this->assertEquals($server_type->name, 'cx11');
    }

    public function testGetByName()
    {
        $server_type = $this->ssh_keys->getByName('cx11');
        $this->assertEquals($server_type->id, 1);
        $this->assertEquals($server_type->name, 'cx11');
    }

    public function testAll()
    {
        $ssh_keys = $this->ssh_keys->all();

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 1);
        $this->assertEquals($ssh_keys[0]->name, 'cx11');
    }

    public function testList()
    {
        $ssh_keys = $this->ssh_keys->list();

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 1);
        $this->assertEquals($ssh_keys[0]->name, 'cx11');
    }
}
