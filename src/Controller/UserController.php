<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/users", name="user.")
 */
class UserController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    /**
     * @Route("/", name=".index", methods={"GET"})
     */
    public function index(
        UserService $userService,
        Request $request
    ): JsonResponse {
        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        if (!$this->user instanceof User) {
            return $this->json(null, 401);
        }

        return $this->json(
            $userService->findPage($paginationDTO, $this->user->getCompany()),
            200,
            [],
            [
                'groups' => 'user.index',
                'pagination' => $paginationDTO,
            ]
        );
    }
}
