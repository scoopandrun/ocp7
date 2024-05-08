<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\Repository\DeviceRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DeviceService
{
    private DeviceRepository $deviceRepository;

    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * Finds a page of devices.
     *
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * 
     * @return Paginator The devices.
     */
    public function findPage(PaginationDTO $paginationDTO): Paginator
    {
        return $this->deviceRepository->findPage(
            $paginationDTO->page,
            $paginationDTO->pageSize
        );
    }
}
