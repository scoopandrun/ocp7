<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\DeviceRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class BrandService
{
    private BrandRepository $brandRepository;
    private DeviceRepository $deviceRepository;

    public function __construct(
        BrandRepository $brandRepository,
        DeviceRepository $deviceRepository
    ) {
        $this->brandRepository = $brandRepository;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * Finds a page of brands.
     *
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * 
     * @return PaginationInterface The brands.
     */
    public function findPage(PaginationDTO $paginationDTO): PaginationInterface
    {
        return $this->brandRepository->findPage(
            $paginationDTO->page,
            $paginationDTO->pageSize
        );
    }

    /**
     * Finds the devices of a brand.
     *
     * @param Brand         $brand         The brand.
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * @param array         $types         The types.
     * 
     * @return PaginationInterface The devices.
     */
    public function findDevices(Brand $brand, PaginationDTO $paginationDTO, array $types): PaginationInterface
    {
        return $this->deviceRepository->findDevicesFromBand(
            $brand,
            $paginationDTO->page,
            $paginationDTO->pageSize,
            $types
        );
    }
}
