<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Device;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/devices", name="device.")
 */
class DeviceController extends AbstractController
{
    /**
     * @Route("/", name=".index", methods={"GET"})
     */
    public function index(
        DeviceService $deviceService,
        Request $request
    ): JsonResponse {
        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        return $this->json(
            $deviceService->findPage($paginationDTO),
            200,
            [],
            [
                'groups' => 'device.index',
                'pagination' => $paginationDTO,
            ]
        );
    }

    /**
     * @Route("/{id}", name=".show", methods={"GET"})
     */
    public function show(Device $device): JsonResponse
    {
        return $this->json(
            $device,
            200,
            [],
            ['groups' => 'device.show']
        );
    }
}
