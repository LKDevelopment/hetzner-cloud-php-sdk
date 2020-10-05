<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\SSHKeys;

use GuzzleHttp\Psr7\Response;
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
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));
        $ssh_key = $this->ssh_keys->get(2323);
        $this->assertEquals($ssh_key->id, 2323);
        $this->assertEquals($ssh_key->name, 'My ssh key');
        $this->assertEquals($ssh_key->public_key, 'ssh-rsa AAAjjk76kgf...Xt');

        $this->assertLastRequestEquals('GET', '/ssh_keys/2323');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_keys.json')));
        $ssh_key = $this->ssh_keys->getByName('My ssh key');
        $this->assertEquals($ssh_key->id, 2323);
        $this->assertEquals($ssh_key->name, 'My ssh key');
        $this->assertEquals($ssh_key->public_key, 'ssh-rsa AAAjjk76kgf...Xt');

        $this->assertLastRequestEquals('GET', '/ssh_keys');
        $this->assertLastRequestQueryParametersContains('name', 'My ssh key');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_keys.json')));
        $ssh_keys = $this->ssh_keys->all();

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 2323);
        $this->assertEquals($ssh_keys[0]->name, 'My ssh key');
        $this->assertEquals($ssh_keys[0]->public_key, 'ssh-rsa AAAjjk76kgf...Xt');

        $this->assertLastRequestEquals('GET', '/ssh_keys');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_keys.json')));
        $ssh_keys = $this->ssh_keys->list()->ssh_keys;

        $this->assertEquals(count($ssh_keys), 1);
        $this->assertEquals($ssh_keys[0]->id, 2323);
        $this->assertEquals($ssh_keys[0]->name, 'My ssh key');
        $this->assertEquals($ssh_keys[0]->public_key, 'ssh-rsa AAAjjk76kgf...Xt');

        $this->assertLastRequestEquals('GET', '/ssh_keys');
    }

    public function testCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));

        $this->ssh_keys->create('my ssh key', 'ssh-rsa AAAjjk76kgf...Xt');

        $this->assertLastRequestEquals('POST', '/ssh_keys');
        $this->assertLastRequestBodyParametersEqual(['name' => 'my ssh key', 'public_key' => 'ssh-rsa AAAjjk76kgf...Xt']);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));

        $sshKey = $this->ssh_keys->get(2323);
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($sshKey->delete());
        $this->assertLastRequestEquals('DELETE', '/ssh_keys/2323');
    }
}
