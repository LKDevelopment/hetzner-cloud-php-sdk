<?php
/**
 * Created by PhpStorm.
 * User: lkaemmerling
 * Date: 05.09.18
 * Time: 10:55.
 */

namespace LKDev\HetznerCloud;

/**
 * Class RequestOpts.
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
    public $label_selector;

    /**
     * RequestOpts constructor.
     * @param $perPage
     * @param $page
     * @param $labelSelector
     */
    public function __construct(int $perPage = null, int $page = null, string $labelSelector = null)
    {
        if ($perPage > HetznerAPIClient::MAX_ENTITIES_PER_PAGE) {
            throw new \InvalidArgumentException('perPage can not be larger than '.HetznerAPIClient::MAX_ENTITIES_PER_PAGE);
        }
        $this->per_page = $perPage;
        $this->page = $page;
        $this->label_selector = $labelSelector;
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

        return count($values) == 0 ? '' : ('?'.http_build_query($values));
    }
}
