<?php

namespace App\Service;

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
     * @param int $page The page number.
     * @param int $limit The number of devices per page.
     * @return Paginator The devices.
     */
    public function findPage(int $page, int $limit): Paginator
    {
        return $this->deviceRepository->findPage($page, $limit);
    }
}
