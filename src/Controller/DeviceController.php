<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Device;
use App\Security\Voter\DeviceVoter;
use App\Service\DeviceService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/api/devices", name="device.")
 * 
 * @OA\Tag(name="Devices")
 */
class DeviceController extends AbstractController
{
    /**
     * @Route("/", name=".index", methods={"GET"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns the list of devices",
     *   @Model(type=Device::class, groups={"device.index"})
     * )
     * 
     * @OA\Parameter(
     *   name="page",
     *   in="query",
     *   description="The page number",
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Parameter(
     *   name="pageSize",
     *   in="query",
     *   description="The number of items per page",
     *   @OA\Schema(type="integer")
     * )
     */
    public function index(
        DeviceService $deviceService,
        Request $request
    ): JsonResponse {
        $this->checkAccessGranted();

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
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns the device",
     *   @Model(type=Device::class, groups={"device.show"})
     * )
     */
    public function show(Device $device): JsonResponse
    {
        $this->checkAccessGranted();

        return $this->json(
            $device,
            200,
            [],
            ['groups' => 'device.show']
        );
    }

    private function checkAccessGranted(): void
    {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::VIEW);
        } catch (AccessDeniedException $e) {
            throw new HttpException(403, "Your company cannot use the API.");
        }
    }
}
