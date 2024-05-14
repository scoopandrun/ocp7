<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Brand;
use App\Entity\Device;
use App\Security\Voter\DeviceVoter;
use App\Service\BrandService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @Route("/api/brands", name="brand")
 * 
 * @OA\Tag(name="Brands")
 */
class BrandController extends AbstractController
{
    private JMSSerializerInterface $jmsSerializer;
    private TagAwareCacheInterface $cache;

    public function __construct(
        JMSSerializerInterface $jmsSerializer,
        TagAwareCacheInterface $cache
    ) {
        $this->jmsSerializer = $jmsSerializer;
        $this->cache = $cache;
    }

    /**
     * @Route("", name=".index", methods={"GET"})
     * 
     * @OA\Response(
     *  response=200,
     *   description="Returns the list of brands",
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
     *       @OA\Items(ref=@Model(type=Brand::class, groups={"brand.index"}))
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
    public function index(BrandService $brandService, Request $request): JsonResponse
    {
        $this->checkAccessGranted();

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 10);

        $cacheKey = "brands_{$page}_{$pageSize}";

        $serializedBrands = $this->cache->get($cacheKey, function (ItemInterface $item) use ($brandService, $page, $pageSize) {
            $item->tag(['brands']);

            $paginationDTO = new PaginationDTO($page, $pageSize);

            $brands = $brandService->findPage($paginationDTO);

            $context = SerializationContext::create()
                ->setGroups([
                    'Default',
                    'items' => ['brand.index']
                ]);

            return $this->jmsSerializer->serialize($brands, 'json', $context);
        });

        return new JsonResponse(
            $serializedBrands,
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
     *   description="Returns the brand",
     *   @Model(type=Brand::class, groups={"brand.show"})
     * )
     */
    public function show(Brand $brand): JsonResponse
    {
        $this->checkAccessGranted();

        $cacheKey = "brand_{$brand->getId()}";

        $serializedBrand = $this->cache->get($cacheKey, function (ItemInterface $item) use ($brand) {
            $item->tag(['brands']);

            $context = SerializationContext::create()->setGroups(['brand.show']);

            return $this->jmsSerializer->serialize($brand, 'json', $context);
        });

        return new JsonResponse(
            $serializedBrand,
            200,
            [],
            true
        );
    }

    /**
     * @Route("/{id}/devices", name=".devices", methods={"GET"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns the list of devices for the brand",
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
     *       @OA\Items(ref=@Model(type=Device::class, groups={"brand.devices"}))
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
    public function devices(
        Brand $brand,
        BrandService $brandService,
        Request $request
    ): JsonResponse {
        $this->checkAccessGranted();

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 10);

        $cacheKey = "brand_{$brand->getId()}_devices_{$page}_{$pageSize}";

        $serializedDevices = $this->cache->get($cacheKey, function (ItemInterface $item) use ($brandService, $brand, $page, $pageSize) {
            $item->tag(['brands', 'devices']);

            $paginationDTO = new PaginationDTO($page, $pageSize);

            $devices = $brandService->findDevices($brand, $paginationDTO);

            $context = SerializationContext::create()
                ->setGroups([
                    'Default',
                    'items' => ['brand.devices']
                ]);

            return $this->jmsSerializer->serialize($devices, 'json', $context);
        });

        return new JsonResponse(
            $serializedDevices,
            200,
            [],
            true
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
