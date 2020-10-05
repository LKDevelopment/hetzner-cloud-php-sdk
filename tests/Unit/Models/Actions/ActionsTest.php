<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Unit\Models\Actions;

use GuzzleHttp\Psr7\Response;
use LKDev\HetznerCloud\Models\Actions\Actions;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    /**
     * @var Actions
     */
    protected $actions;

    public function setUp(): void
    {
        parent::setUp();
        $this->actions = new Actions($this->hetznerApi->getHttpClient());
    }

    public function testGet()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/action.json')));
        $datacenter = $this->actions->get(13);
        $this->assertEquals($datacenter->id, 13);
        $this->assertEquals($datacenter->command, 'start_server');
        $this->assertLastRequestEquals('GET', '/actions/13');
    }

    public function testGetByName()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->actions->getByName('start_server');
    }

    public function testAll()
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__.'/fixtures/actions.json')));
        $actions = $this->actions->all();

        $this->assertEquals(count($actions), 1);
        $this->assertEquals($actions[0]->id, 13);
        $this->assertEquals($actions[0]->command, 'start_server');
        $this->assertLastRequestEquals('GET', '/actions');
    }
}
