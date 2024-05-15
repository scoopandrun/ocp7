<?php

namespace App\Controller;

use App\DTO\PaginationDTO;
use App\Entity\Device;
use App\Security\Voter\DeviceVoter;
use App\Service\DeviceService;
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
 * @Route("/api/devices", name="device")
 * 
 * @OA\Tag(name="Devices")
 */
class DeviceController extends BaseController
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
     *   response=200,
     *   description="Returns the list of devices",
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
     *       @OA\Items(ref=@Model(type=Device::class, groups={"device.index"}))
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
        DeviceService $deviceService,
        Request $request
    ): JsonResponse {
        $this->checkAccessGranted(DeviceVoter::VIEW, null, "Your company cannot use the API.");

        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 10);

        $cacheKey = "devices_{$page}_{$pageSize}";

        $serializedDevices = $this->cache->get($cacheKey, function (ItemInterface $item) use ($deviceService, $page, $pageSize) {
            $item->tag(['devices']);

            $paginationDTO = new PaginationDTO($page, $pageSize);

            $devices = $deviceService->findPage($paginationDTO);

            $context = (new SerializationContext())
                ->setGroups([
                    'Default',
                    'items' => [
                        'device.index',
                        'brand' => ['device.index'],
                    ]
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

    /**
     * @Route("/{id}", name=".show", methods={"GET"})
     * 
     * @OA\Response(
     *   response=200,
     *   description="Returns the device",
     *   @Model(type=Device::class, groups={"device.show"})
     * )
     */
    public function show(Device $device): JsonResponse
    {
        $this->checkAccessGranted(DeviceVoter::VIEW, null, "Your company cannot use the API.");

        $cacheKey = "device_{$device->getId()}";

        $serializedDevice = $this->cache->get($cacheKey, function (ItemInterface $item) use ($device) {
            $item->tag(['devices']);

            $context = (new SerializationContext())->setGroups(['device.show']);

            return $this->jmsSerializer->serialize($device, 'json', $context);
        });

        return new JsonResponse(
            $serializedDevice,
            200,
            [],
            true
        );
    }
}
