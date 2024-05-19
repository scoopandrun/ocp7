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

    public function findPage(int $page, int $limit, array $brands, array $types): PaginationInterface
    {
        $qb = $this->createQueryBuilder('d')
            ->orderBy('d.id', 'ASC');

        if (!empty($brands)) {
            $qb->leftJoin('d.brand', 'b')
                ->andWhere('b.name IN (:brands)')
                ->setParameter('brands', $brands);
        }

        if (!empty($types)) {
            $qb->andWhere('d.type IN (:types)')
                ->setParameter('types', $types);
        }

        $paginator = $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit,
            ['distinct' => false]
        );

        return $paginator;
    }

    public function findDevicesFromBand(Brand $brand, int $page, int $limit, array $types): PaginationInterface
    {
        $qb =  $this->createQueryBuilder('d')
            ->where('d.brand = :brand')
            ->setParameter('brand', $brand)
            ->orderBy('d.id', 'ASC');

        if (!empty($types)) {
            $qb->andWhere('d.type IN (:types)')
                ->setParameter('types', $types);
        }

        $paginator = $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit,
            ['distinct' => false]
        );

        return $paginator;
    }
}
