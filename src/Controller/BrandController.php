<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Brand;
use App\Security\Voter\DeviceVoter;
use App\Service\BrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/api/brands", name="brand.")
 */
class BrandController extends AbstractController
{
    /**
     * @Route("/", name=".index", methods={"GET"})
     */
    public function index(
        BrandService $brandService,
        Request $request
    ): JsonResponse {
        $this->checkAccessGranted();

        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        return $this->json(
            $brandService->findPage($paginationDTO),
            200,
            [],
            [
                'groups' => 'brand.index',
                'pagination' => $paginationDTO,
            ]
        );
    }

    /**
     * @Route("/{id}", name=".show", methods={"GET"})
     */
    public function show(Brand $brand): JsonResponse
    {
        $this->checkAccessGranted();

        return $this->json(
            $brand,
            200,
            [],
            ['groups' => 'brand.show']
        );
    }

    /**
     * @Route("/{id}/devices", name=".devices", methods={"GET"})
     */
    public function devices(
        Brand $brand,
        BrandService $brandService,
        Request $request
    ): JsonResponse {
        $this->checkAccessGranted();

        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        return $this->json(
            $brandService->findDevices($brand, $paginationDTO),
            200,
            [],
            [
                'groups' => 'brand.devices',
                'pagination' => $paginationDTO,
            ]
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
