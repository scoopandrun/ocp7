<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/users", name="user")
 * 
 * @OA\Tag(name="Users")
 */
class UserController extends AbstractController
{
    private User $user;
    private JMSSerializerInterface $jmsSerializer;

    public function __construct(Security $security, JMSSerializerInterface $jmsSerializer)
    {
        $this->user = $security->getUser();
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @Route("/", name=".index", methods={"GET"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns the list of users for the customer",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *       property="currentPageNumber",
     *       type="integer"
     *     ),
     *     @OA\Property(
     *       property="numItemsPerPage",
     *       type="integer"
     *     ),
     *     @OA\Property(
     *       property="items",
     *       type="array",
     *       @OA\Items(ref=@Model(type=User::class, groups={"user.index"}))
     *     ),
     *     @OA\Property(
     *       property="totalCount",
     *       type="integer"
     *     )
     *   )
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

        $users = $userService->findPage($paginationDTO, $this->user->getCompany());

        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'items' => ['user.index']
            ]);
        $serializedUsers = $this->jmsSerializer->serialize($users, 'json', $context);

        return new JsonResponse(
            $serializedUsers,
            200,
            [],
            true
        );
    }

    /**
     * @Route("/{id}", name=".show", methods={"GET"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns a specific user",
     *   @Model(type=User::class, groups={"user.show"})
     * )
     * 
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="The user ID",
     *   @OA\Schema(type="integer")
     * )
     */
    public function show(User $user): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        } catch (AccessDeniedException $e) {
            throw new HttpException(403, "You cannot view another company's users");
        }

        $context = SerializationContext::create()->setGroups(['user.show']);
        $serializedUser = $this->jmsSerializer->serialize($user, 'json', $context);

        return new JsonResponse(
            $serializedUser,
            200,
            [],
            true
        );
    }

    /**
     * @Route("/", name=".create", methods={"POST"})
     * 
     * @OA\Response(
     *   response=201,
     *   description="Creates a new user",
     *   headers={
     *     @OA\Header(
     *       header="Location",
     *       description="The URL to the new user",
     *       @OA\Schema(type="string")
     *     )
     *   },
     *   @Model(type=User::class, groups={"user.show"})
     * )
     * 
     * @OA\Response(
     *   response=400,
     *   description="Validation error",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Schema(ref="#/components/schemas/ConstraintViolations")
     *   )
     * )
     * 
     * @OA\RequestBody(
     *   @Model(type=User::class, groups={"user.create"})
     * )
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
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

        $url = $urlGenerator->generate('user.show', ['id' => $user->getId()]);

        $context = SerializationContext::create()->setGroups(['user.show']);
        $serializedUser = $this->jmsSerializer->serialize($user, 'json', $context);

        return new JsonResponse(
            $serializedUser,
            201,
            ["Location" => $url],
            true
        );
    }

    /**
     * @Route("/{id}", name=".delete", methods={"DELETE"})
     * 
     * @OA\Response(
     *   response=204,
     *   description="Deletes a user"
     * )
     * 
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="The user ID",
     *   @OA\Schema(type="integer")
     * )
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
