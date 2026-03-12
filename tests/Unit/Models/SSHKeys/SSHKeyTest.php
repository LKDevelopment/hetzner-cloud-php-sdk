<?php

namespace LKDev\Tests\Unit\Models\SSHKeys;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKey;
use LKDev\Tests\TestCase;

class SSHKeyTest extends TestCase
{
    /**
     * @var SSHKey
     */
    protected $sshKey;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/ssh_key.json'));
        $this->sshKey = SSHKey::parse($tmp->ssh_key);
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));
        $updated = $this->sshKey->update(['name' => 'New Name']);
        $this->assertInstanceOf(SSHKey::class, $updated);
        $this->assertLastRequestEquals('PUT', '/ssh_keys/2323');
        $this->assertLastRequestBodyParametersEqual(['name' => 'New Name']);
    }

    public function testChangeName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));
        $updated = $this->sshKey->changeName('New Name');
        $this->assertInstanceOf(SSHKey::class, $updated);
        $this->assertLastRequestEquals('PUT', '/ssh_keys/2323');
        $this->assertLastRequestBodyParametersEqual(['name' => 'New Name']);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($this->sshKey->delete());
        $this->assertLastRequestEquals('DELETE', '/ssh_keys/2323');
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/ssh_key.json')));
        $reloaded = $this->sshKey->reload();
        $this->assertInstanceOf(SSHKey::class, $reloaded);
        $this->assertLastRequestEquals('GET', '/ssh_keys/2323');
    }

    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/ssh_key.json'));
        $parsed = SSHKey::parse($tmp->ssh_key);
        $this->assertEquals($this->sshKey->id, $parsed->id);
        $this->assertEquals($this->sshKey->name, $parsed->name);
        $this->assertEquals($this->sshKey->fingerprint, $parsed->fingerprint);
        $this->assertEquals($this->sshKey->public_key, $parsed->public_key);
        $this->assertEquals($this->sshKey->labels, $parsed->labels);
    }
}
