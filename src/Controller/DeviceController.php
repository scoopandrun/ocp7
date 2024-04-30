<?php

namespace App\Controller;

use App\Entity\Device;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeviceController extends AbstractController
{
    /**
     * @Route("/devices", name="device.index", methods={"GET"})
     */
    public function index(
        DeviceService $deviceService,
        Request $request
    ): JsonResponse {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        return $this->json(
            $deviceService->findPage($page, $limit),
            200,
            [],
            ['groups' => 'device.index']
        );
    }

    /**
     * @Route("/devices/{id}", name="device.show", methods={"GET"})
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
