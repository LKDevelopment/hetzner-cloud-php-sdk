<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 11.07.18
 * Time: 18:31
 */

namespace Tests\Unit;

use LKDev\HetznerCloud\Models\Datacenters\Datacenters;
use LKDev\HetznerCloud\Models\ISOs\ISOs;
use Tests\TestCase;

/**
 *
 */
class ISOsTest extends TestCase
{
    /**
     * @var ISOs
     */
    protected $isos;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->isos = new ISOs($this->hetznerApi->getHttpClient());
    }

    /**
     *
     */
    public function testGet()
    {
        $iso = $this->isos->get(4711);
        $this->assertEquals($iso->id, 4711);
        $this->assertEquals($iso->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');
    }

    /**
     *
     */
    public function testAll()
    {
        $isos = $this->isos->all();

        $this->assertEquals(count($isos), 1);
        $this->assertEquals($isos[0]->id, 4711);
        $this->assertEquals($isos[0]->name, 'FreeBSD-11.0-RELEASE-amd64-dvd1');

    }
}
