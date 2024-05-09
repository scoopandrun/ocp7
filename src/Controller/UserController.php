<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
    }

    /**
     * @Route("/", name=".index", methods={"GET"})
     */
    public function index(
        UserService $userService,
        Request $request
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(UserVoter::LIST);
        } catch (AccessDeniedException $e) {
            throw new HttpException(403, "You cannot view users");
        }

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
        try {
            $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        } catch (AccessDeniedException $e) {
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
        try {
            $this->denyAccessUnlessGranted(UserVoter::CREATE);
        } catch (AccessDeniedException $e) {
            throw new HttpException(403, "You cannot create users");
        }

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $message = $serializer->serialize($errors, 'json');
            throw new HttpException(400, $message);
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
        try {
            $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);
        } catch (AccessDeniedException $e) {
            throw new HttpException(403, "You cannot delete another company's users");
        }

        $entityManager->remove($user, true);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
