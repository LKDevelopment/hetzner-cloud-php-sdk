<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\Certificates;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Certificates\Certificates;
use Tests\TestCase;

class CertificatesTest extends TestCase
{
    /**
     * @var  \LKDev\HetznerCloud\Models\Certificates\Certificates
     */
    protected $certificates;

    public function setUp(): void
    {
        parent::setUp();
        $this->certificates = new Certificates($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificate.json')));
        $certificate = $this->certificates->get(897);
        $this->assertEquals($certificate->id, 897);
        $this->assertEquals($certificate->name, 'my website cert');
        $this->assertEquals($certificate->certificate, "-----BEGIN CERTIFICATE-----\n...");

        $this->assertLastRequestEquals('GET', '/certificates/897');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificates.json')));
        $certificate = $this->certificates->getByName('my website cert');
        $this->assertEquals($certificate->id, 897);
        $this->assertEquals($certificate->name, 'my website cert');
        $this->assertEquals($certificate->certificate, "-----BEGIN CERTIFICATE-----\n...");

        $this->assertLastRequestEquals('GET', '/certificates');
        $this->assertLastRequestQueryParametersContains('name', 'my website cert');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificates.json')));
        $certificates = $this->certificates->all();

        $this->assertEquals(count($certificates), 1);
        $this->assertEquals($certificates[0]->id, 897);
        $this->assertEquals($certificates[0]->name, 'my website cert');
        $this->assertEquals($certificates[0]->certificate, "-----BEGIN CERTIFICATE-----\n...");

        $this->assertLastRequestEquals('GET', '/certificates');
    }

    public function testList()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificates.json')));
        $certificates = $this->certificates->list()->certificates;

        $this->assertEquals(count($certificates), 1);
        $this->assertEquals($certificates[0]->id, 897);
        $this->assertEquals($certificates[0]->name, 'my website cert');
        $this->assertEquals($certificates[0]->certificate, "-----BEGIN CERTIFICATE-----\n...");

        $this->assertLastRequestEquals('GET', '/certificates');
    }

    public function testCreate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificate.json')));

        $this->certificates->create('my cert', "-----BEGIN CERTIFICATE-----\n...", "-----BEGIN PRIVATE KEY-----\n...");

        $this->assertLastRequestEquals('POST', '/certificates');
        $this->assertLastRequestBodyParametersEqual(['name' => 'my cert', 'certificate' => "-----BEGIN CERTIFICATE-----\n...", 'private_key' =>  "-----BEGIN PRIVATE KEY-----\n..."]);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/certificate.json')));

        $certificate = $this->certificates->get(897);
        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($certificate->delete());
        $this->assertLastRequestEquals('DELETE', '/certificates/897');
    }
}
