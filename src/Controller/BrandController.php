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

/**
 * @Route("/api/brands", name="brand")
 * 
 * @OA\Tag(name="Brands")
 */
class BrandController extends AbstractController
{
    private JMSSerializerInterface $jmsSerializer;

    public function __construct(JMSSerializerInterface $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @Route("/", name=".index", methods={"GET"})
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

        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        $brands = $brandService->findPage($paginationDTO);

        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'items' => ['brand.index']
            ]);
        $serializedBrands = $this->jmsSerializer->serialize($brands, 'json', $context);

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

        $context = SerializationContext::create()->setGroups(['brand.show']);
        $serializedBrand = $this->jmsSerializer->serialize($brand, 'json', $context);

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

        $paginationDTO = new PaginationDTO(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        $devices = $brandService->findDevices($brand, $paginationDTO);

        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'items' => ['brand.devices']
            ]);
        $serializedDevices = $this->jmsSerializer->serialize($devices, 'json', $context);

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
