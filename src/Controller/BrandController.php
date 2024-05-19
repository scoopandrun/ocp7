<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Brand;
use App\Entity\Device;
use App\Security\Voter\DeviceVoter;
use App\Service\BrandService;
use App\Service\RequestService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @Route("/api/brands", name="brand")
 * 
 * @OA\Tag(name="Brands")
 */
class BrandController extends BaseController
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
        $this->checkAccessGranted(DeviceVoter::VIEW, null, "The customer you are attached to cannot use the API.");

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

        $response = new JsonResponse(
            $serializedBrands,
            200,
            [],
            true
        );

        $response->setEtag(md5($response->getContent()));

        return $response;
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
        $this->checkAccessGranted(DeviceVoter::VIEW, null, "The customer you are attached to cannot use the API.");

        $cacheKey = "brand_{$brand->getId()}";

        $serializedBrand = $this->cache->get($cacheKey, function (ItemInterface $item) use ($brand) {
            $item->tag(['brands']);

            $context = SerializationContext::create()->setGroups(['brand.show']);

            return $this->jmsSerializer->serialize($brand, 'json', $context);
        });

        $response = new JsonResponse(
            $serializedBrand,
            200,
            [],
            true
        );

        $response->setEtag(md5($response->getContent()));

        return $response;
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
     * 
     * @OA\Parameter(
     *   name="types",
     *   in="query",
     *   description="Comma-separated types of devices",
     *   @OA\Schema(type="string"),
     *   example="phone,tablet"
     * )
     */
    public function devices(
        Brand $brand,
        BrandService $brandService,
        Request $request,
        RequestService $requestService
    ): JsonResponse {
        $this->checkAccessGranted(DeviceVoter::VIEW, null, "The customer you are attached to cannot use the API.");

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 10);
        $types = $requestService->getQueryParameterAsArray('types');

        $cacheKey = "brand_{$brand->getId()}_devices_{$page}_{$pageSize}_" . join("-", $types);

        $serializedDevices = $this->cache->get(
            $cacheKey,
            function (ItemInterface $item) use ($brandService, $brand, $page, $pageSize, $types) {
                $item->tag(['brands', 'devices']);

                $paginationDTO = new PaginationDTO($page, $pageSize);

                $devices = $brandService->findDevices($brand, $paginationDTO, $types);

                $context = SerializationContext::create()
                    ->setGroups([
                        'Default',
                        'items' => ['brand.devices']
                    ]);

                return $this->jmsSerializer->serialize($devices, 'json', $context);
            }
        );

        $response = new JsonResponse(
            $serializedDevices,
            200,
            [],
            true
        );

        $response->setEtag(md5($response->getContent()));

        return $response;
    }
}
