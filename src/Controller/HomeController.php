<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api", name="home")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("", name=".index", methods={"GET"})
     */
    public function index(
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $baseUrl = $urlGenerator->getContext()->getScheme() . '://' . $urlGenerator->getContext()->getHost();

        return $this->json([
            'message' => 'Welcome to the BileMo API',
            'documentation' => [
                'HTML' => $baseUrl . '/api/doc',
                'JSON' => $baseUrl . '/api/doc.json',
            ]
        ]);
    }
}
