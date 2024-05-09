<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Brand;
use App\Service\BrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->json(
            $brand,
            200,
            [],
            ['groups' => 'brand.show']
        );
    }
}
