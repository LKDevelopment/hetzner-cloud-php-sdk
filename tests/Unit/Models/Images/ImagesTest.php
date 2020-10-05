<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Images\Images;
use Tests\TestCase;

class ImagesTest extends TestCase
{
    /**
     * @var Images
     */
    protected $images;

    public function setUp(): void
    {
        parent::setUp();
        $this->images = new Images($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image.json')));
        $image = $this->images->get(4711);
        $this->assertEquals($image->id, 4711);
        $this->assertEquals($image->name, 'ubuntu-20.04');

        $this->assertEmpty($image->labels);

        $this->assertLastRequestEquals('GET', '/images/4711');
    }

    public function testGetByName()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/images.json')));
        $image = $this->images->getByName('ubuntu-20.04');
        $this->assertEquals($image->id, 4711);
        $this->assertEquals($image->name, 'ubuntu-20.04');

        $this->assertEmpty($image->labels);
        $this->assertLastRequestEquals('GET', '/images');
        $this->assertLastRequestQueryParametersContains('name', 'ubuntu-20.04');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/images.json')));
        $images = $this->images->all();

        $this->assertEquals(count($images), 1);
        $this->assertEquals($images[0]->id, 4711);
        $this->assertEquals($images[0]->name, 'ubuntu-20.04');
        $this->assertLastRequestEquals('GET', '/images');
    }

    public function testUpdate()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image.json')));
        $image = $this->images->get(4711);

        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image.json')));
        $image->update(['name' => 'My new Image description', 'type' => 'snapshot']);
        $this->assertLastRequestEquals('PUT', '/images/4711');
        $this->assertLastRequestBodyParametersEqual(['name' => 'My new Image description', 'type' => 'snapshot']);
    }

    public function testDelete()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image.json')));
        $image = $this->images->get(4711);

        $this->mockHandler->append(new Response(204, []));
        $this->assertTrue($image->delete());
        $this->assertLastRequestEquals('DELETE', '/images/4711');
    }

    public function testChangeProtection()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image.json')));
        $image = $this->images->get(4711);

        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/image_action_change_protection.json')));
        $apiResponse = $image->changeProtection();
        $this->assertEquals('change_protection', $apiResponse->action->command);
        $this->assertEquals($image->id, $apiResponse->action->resources[0]->id);
        $this->assertEquals('image', $apiResponse->action->resources[0]->type);

        $this->assertLastRequestEquals('POST', '/images/4711/actions/change_protection');
        $this->assertLastRequestBodyParametersEqual(['delete' => true]);
    }
}
