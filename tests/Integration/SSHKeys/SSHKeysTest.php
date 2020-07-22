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

    public function setUp(): void
    {
        parent::setUp();
        $this->ssh_keys = new SSHKeys($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $ssh_key = $this->ssh_keys->get(2323);
        $this->assertEquals($ssh_key->id, 2323);
        $this->assertEquals($ssh_key->name, 'My ssh key');
        $this->assertEquals($ssh_key->public_key, 'ssh-rsa AAAjjk76kgf...Xt');
    }

    public function testGetByName()
    {
        $ssh_key = $this->ssh_keys->getByName('My ssh key');
        $this->assertEquals($ssh_key->id, 2323);
        $this->assertEquals($ssh_key->name, 'My ssh key');
        $this->assertEquals($ssh_key->public_key, 'ssh-rsa AAAjjk76kgf...Xt');
    }

    public function testAll()
    {
        $ssh_keys = $this->ssh_keys->all();

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 2323);
        $this->assertEquals($ssh_keys[0]->name, 'My ssh key');
        $this->assertEquals($ssh_keys[0]->public_key, 'ssh-rsa AAAjjk76kgf...Xt');
    }

    public function testList()
    {
        $ssh_keys = $this->ssh_keys->list()->ssh_keys;

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 2323);
        $this->assertEquals($ssh_keys[0]->name, 'My ssh key');
        $this->assertEquals($ssh_keys[0]->public_key, 'ssh-rsa AAAjjk76kgf...Xt');
    }
}
