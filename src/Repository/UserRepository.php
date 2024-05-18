<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function findPage(int $page, int $limit, Customer $customer): PaginationInterface
    {
        $paginator = $this->paginator->paginate(
            $this->createQueryBuilder('u')
                ->leftJoin('u.customer', 'c')
                ->select('u', 'c')
                ->where('c = :customer')
                ->setParameter('customer', $customer)
                ->orderBy('u.id', 'ASC')
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            $page,
            $limit
        );

        return $paginator;
    }

    /**
     * Checks if an email is already taken.
     * 
     * @param string $email The email to check.
     * @param int|null $id Optional. The ID of the user to exclude from the check.
     * 
     * @return bool True if the email is already taken, false otherwise.
     */
    public function checkEmailAlreadyTaken(string $email, ?int $id = null): bool
    {
        $qb =  $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        if ($id) {
            $qb->andWhere('u.id != :id')
                ->setParameter('id', $id);
        }

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }
}
