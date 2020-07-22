<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31.
 */

namespace Tests\Integration;

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
        $datacenter = $this->actions->get(13);
        $this->assertEquals($datacenter->id, 13);
        $this->assertEquals($datacenter->command, 'start_server');
    }

    public function testGetByName()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->actions->getByName('start_server');
    }

    public function testAll()
    {
        $actions = $this->actions->all();

        $this->assertEquals(count($actions), 1);
        $this->assertEquals($actions[0]->id, 13);
        $this->assertEquals($actions[0]->command, 'start_server');
    }
}
