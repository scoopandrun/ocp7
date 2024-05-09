<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/users", name="user.")
 */
class UserController extends AbstractController
{
    private User $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();

        if (!$this->user instanceof User) {
            throw new HttpException(
                401,
                "You must be logged in to view users",
                null,
                ['WWW-Authenticate' => 'Bearer']
            );
        }
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

    /**
     * @Route("/{id}", name=".show", methods={"GET"})
     */
    public function show(User $user): JsonResponse
    {
        if ($user->getCompany() !== $this->user->getCompany()) {
            throw new HttpException(403, "You cannot view another company's users");
        }

        return $this->json(
            $user,
            200,
            [],
            ['groups' => 'user.show']
        );
    }

    /**
     * @Route("/", name=".create", methods={"POST"})
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            throw new HttpException(400, json_encode($errors));
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            201,
            [],
            ['groups' => 'user.show']
        );
    }

    /**
     * @Route("/{id}", name=".delete", methods={"DELETE"})
     */
    public function delete(
        User $user,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if ($user->getCompany() !== $this->user->getCompany()) {
            throw new HttpException(403, "You cannot delete another company's users");
        }

        $entityManager->remove($user, true);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}