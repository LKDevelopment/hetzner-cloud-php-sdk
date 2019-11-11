<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 05.09.18
 * Time: 10:55
 */

namespace LKDev\HetznerCloud;


/**
 * Class RequestOpts
 * @package LKDev\HetznerCloud
 */
class RequestOpts
{

    /**
     * @var int
     */
    public $per_page;


    /**
     * @var int
     */
    public $page;


    /**
     * @var string
     */
    public $labelSelector;

    /**
     * RequestOpts constructor.
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(int $perPage = null, int $page = null, string $labelSelector = null)
    {
        $this->per_page = $perPage;
        $this->page = $page;
        $this->labelSelector = $labelSelector;
    }

    /**
     * @return string
     */
    public function buildQuery()
    {
        $values = collect(get_object_vars($this))
            ->filter(function ($var) {
                return $var != null;
            })->toArray();
        return count($values) == 0 ? '' : ('?' . http_build_query($values));
    }
}
