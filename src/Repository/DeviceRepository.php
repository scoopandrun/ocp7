<?php

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Device>
 *
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Device::class);
        $this->paginator = $paginator;
    }

    public function findPage(int $page, int $limit): PaginationInterface
    {
        $paginator = $this->paginator->paginate(
            $this->createQueryBuilder('d')
                ->orderBy('d.id', 'ASC')
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            $page,
            $limit
        );

        return $paginator;
    }

    public function findDevicesFromBand(Brand $brand, int $page, int $limit): PaginationInterface
    {
        $paginator = $this->paginator->paginate(
            $this->createQueryBuilder('d')
                ->where('d.brand = :brand')
                ->setParameter('brand', $brand)
                ->orderBy('d.id', 'ASC')
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            $page,
            $limit
        );

        return $paginator;
    }
}
