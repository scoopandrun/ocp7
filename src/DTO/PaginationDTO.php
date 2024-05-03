<?php

namespace App\DTO;

/**
 * Represents a pagination data transfer object.
 */
class PaginationDTO
{
    /**
     * The current page number.
     *
     * @var int
     */
    public int $page = 1;

    /**
     * The maximum number of items per page.
     *
     * @var int
     */
    public int $limit = 10;

    /**
     * Initializes a new instance of the PaginationDTO class.
     *
     * @param int $page The current page number.
     * @param int $limit The maximum number of items per page.
     */
    public function __construct(int $page, int $limit)
    {
        $this->page = max(1, $page);

        // If $limit is less than 1, set it to the default (10)
        if ($limit < 1) {
            $this->limit = 10;
        } else {
            $this->limit = $limit;
        }
    }
}
