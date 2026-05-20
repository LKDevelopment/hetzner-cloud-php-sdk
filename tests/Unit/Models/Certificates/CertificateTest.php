<?php

namespace LKDev\Tests\Unit\Models\Certificates;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Certificates\Certificate;
use LKDev\Tests\TestCase;

class CertificateTest extends TestCase
{
    /**
     * @var Certificate
     */
    protected $certificate;

    public function setUp(): void
    {
        parent::setUp();
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/certificate.json'));
        $this->certificate = Certificate::parse($tmp->certificate);
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificate.json')));
        $updated = $this->certificate->update(['name' => 'New Name']);
        $this->assertInstanceOf(Certificate::class, $updated);
        $this->assertLastRequestEquals('PUT', '/certificates/897');
        $this->assertLastRequestBodyParametersEqual(['name' => 'New Name']);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($this->certificate->delete());
        $this->assertLastRequestEquals('DELETE', '/certificates/897');
    }

    public function testReload()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificate.json')));
        $reloaded = $this->certificate->reload();
        $this->assertInstanceOf(Certificate::class, $reloaded);
        $this->assertLastRequestEquals('GET', '/certificates/897');
    }

    public function testParse()
    {
        $tmp = json_decode(file_get_contents(__DIR__.'/fixtures/certificate.json'));
        $parsed = Certificate::parse($tmp->certificate);
        $this->assertEquals($this->certificate->id, $parsed->id);
        $this->assertEquals($this->certificate->name, $parsed->name);
        $this->assertEquals($this->certificate->certificate, $parsed->certificate);
        $this->assertEquals($this->certificate->labels, $parsed->labels);
    }
}
