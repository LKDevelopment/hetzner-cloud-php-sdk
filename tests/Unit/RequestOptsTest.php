<?php

namespace Tests\Unit;

use LKDev\HetznerCloud\RequestOpts;
use Tests\TestCase;

class RequestOptsTest extends TestCase
{
    public function buildQueryTestProvider()
    {
        return [
            [
                20, 5, 'key=value', '?per_page=20&page=5&label_selector=key%3Dvalue',
            ],
            [
                20, 5, null, '?per_page=20&page=5',
            ],
            [
                20, null, 'key=value', '?per_page=20&label_selector=key%3Dvalue',
            ],
            [
                null, null, 'key=value', '?label_selector=key%3Dvalue',
            ],
            [
                null, 5, 'key=value', '?page=5&label_selector=key%3Dvalue',
            ],
            [
                null, null, null, '',
            ],
        ];
    }

    /**
     * @dataProvider buildQueryTestProvider
     * @test
     */
    public function buildQuery($perPage, $page, $labelSelector, $expectedResult)
    {
        $r = new RequestOpts($perPage, $page, $labelSelector);
        $this->assertEquals($expectedResult, $r->buildQuery());
    }

    /**
     * @test
     */
    public function createRequestOptsWithGreaterPerPageThanLimitShouldThrowAnError()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RequestOpts(100);
    }
}
