<?php

namespace LKDev\HetznerCloud\Models;

// This is a read only model, that does not have any logic. Just a stupid dataholder.
class Pagination extends Model
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $per_page;

    /**
     * @var int
     */
    public $previous_page;
    /**
     * @var int
     */
    public $next_page;
    /**
     * @var int
     */
    public $last_page;
    /**
     * @var int
     */
    public $total_entries;

    /**
     * Pagination constructor.
     * @param int $page
     * @param int $per_page
     * @param int $previous_page
     * @param int $next_page
     * @param int $last_page
     * @param int $total_entries
     */
    public function __construct($page, $per_page, $previous_page, $next_page, $last_page, $total_entries)
    {
        $this->page = $page;
        $this->per_page = $per_page;
        $this->previous_page = $previous_page;
        $this->next_page = $next_page;
        $this->last_page = $last_page;
        $this->total_entries = $total_entries;
        // Force getting the default http client
        parent::__construct(null);
    }

    /**
     * @param $input
     * @return \LKDev\HetznerCloud\Models\Pagination|null|static
     */
    public static function parse($input)
    {
        if ($input == null) {
            return;
        }

        return new self($input->page, $input->per_page, $input->previous_page, $input->next_page, $input->last_page, $input->total_entries);
    }
}
