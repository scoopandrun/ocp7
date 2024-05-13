<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="home")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name=".index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to the BileMo API',
        ]);
    }
}
