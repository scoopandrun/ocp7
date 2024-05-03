<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\Repository\BrandRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BrandService
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Finds a page of devices.
     *
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * @return Paginator The devices.
     */
    public function findPage(PaginationDTO $paginationDTO): Paginator
    {
        return $this->brandRepository->findPage(
            $paginationDTO->page,
            $paginationDTO->limit
        );
    }
}
