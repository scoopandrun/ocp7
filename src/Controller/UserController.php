<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @Route("/api/users", name="user")
 * 
 * @OA\Tag(name="Users")
 */
class UserController extends BaseController
{
    private User $user;
    private JMSSerializerInterface $jmsSerializer;
    private TagAwareCacheInterface $cache;

    public function __construct(
        Security $security,
        JMSSerializerInterface $jmsSerializer,
        TagAwareCacheInterface $cache
    ) {
        $this->user = $security->getUser();
        $this->jmsSerializer = $jmsSerializer;
        $this->cache = $cache;
    }

    /**
     * @Route("", name=".index", methods={"GET"})
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
        $this->checkAccessGranted(UserVoter::LIST, null, "You cannot view users");

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 10);

        $customer = $this->user->getCustomer();

        $cacheKey = "users_{$customer->getId()}_{$page}_{$pageSize}";

        $serializedUsers = $this->cache->get($cacheKey, function (ItemInterface $item) use ($userService, $customer, $page, $pageSize) {
            $item->tag(['users']);

            $paginationDTO = new PaginationDTO($page, $pageSize);

            $users = $userService->findPage($paginationDTO, $customer);

            $context = SerializationContext::create()
                ->setGroups([
                    'Default',
                    'items' => ['user.index']
                ]);

            return $this->jmsSerializer->serialize($users, 'json', $context);
        });

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
        $this->checkAccessGranted(UserVoter::VIEW, $user, "You cannot view another customer's users");

        $cacheKey = "user_{$user->getId()}";

        $serializedUser = $this->cache->get($cacheKey, function (ItemInterface $item) use ($user) {
            $item->tag(['users']);

            $context = SerializationContext::create()->setGroups(['user.show']);

            return $this->jmsSerializer->serialize($user, 'json', $context);
        });

        return new JsonResponse(
            $serializedUser,
            200,
            [],
            true
        );
    }

    /**
     * @Route("", name=".create", methods={"POST"})
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
     *     ref="#/components/schemas/ConstraintViolations"
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
        UrlGeneratorInterface $urlGenerator,
        UserService $userService
    ): JsonResponse {
        $this->checkAccessGranted(UserVoter::CREATE, null, "You cannot create users");

        /** @var UserDTO */
        $userDTO = $serializer->deserialize($request->getContent(), UserDTO::class, 'json');

        $errors = $validator->validate($userDTO, null, ['Default', 'create']);

        if (count($errors) > 0) {
            $message = $serializer->serialize($errors, 'json');
            throw new HttpException(400, $message);
        }

        $user = $userService->fillInUserEntityFromUserInformationDTO($userDTO);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->cache->invalidateTags(['users']);

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
     * @Route("/{id}", name=".update", methods={"PUT"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Updates a user",
     *   @Model(type=User::class, groups={"user.show"})
     * )
     * 
     * @OA\Response(
     *   response=400,
     *   description="Validation error",
     *   @OA\JsonContent(
     *     ref="#/components/schemas/ConstraintViolations"
     *   )
     * )
     * 
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="The user ID",
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\RequestBody(
     *   @Model(type=User::class, groups={"user.update"})
     * )
     */
    public function update(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        UserService $userService
    ): JsonResponse {
        $this->checkAccessGranted(UserVoter::UPDATE, $user, "You cannot update another customer's users");

        /** @var UserDTO */
        $userDTO = $serializer->deserialize($request->getContent(), UserDTO::class, 'json');

        $userDTO->setId($user->getId());

        $errors = $validator->validate($userDTO, null, ['Default', 'update']);

        if (count($errors) > 0) {
            $message = $serializer->serialize($errors, 'json');
            throw new HttpException(400, $message);
        }

        $user = $userService->fillInUserEntityFromUserInformationDTO($userDTO, $user);

        $entityManager->flush();

        $this->cache->invalidateTags(['users']);

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
        $this->checkAccessGranted(UserVoter::DELETE, $user, "You cannot delete another customer's users");

        $entityManager->remove($user, true);
        $entityManager->flush();

        $this->cache->invalidateTags(['users']);

        return $this->json(null, 204);
    }
}
