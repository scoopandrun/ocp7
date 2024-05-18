<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\DTO\UserDTO;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Fills in a user entity with information from a user data transfer object.
     * 
     * @param UserDTO   $userDTO The user data transfer object.
     * @param null|User $user    Optional. The user entity to fill in. If not provided, a new user entity will be created.
     * 
     * @return User The user entity.
     */
    public function fillInUserEntityFromUserInformationDTO(UserDTO $userDTO, ?User $user = null): User
    {
        $user ??= new User();

        if ($userDTO->getEmail()) {
            $user->setEmail($userDTO->getEmail());
        }

        if ($userDTO->getFullname()) {
            $user->setFullname($userDTO->getFullname());
        }

        if ($userDTO->getPassword()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $userDTO->getPassword()
                )
            );

            $userDTO->eraseCredentials();
        }

        $user->setCustomer($userDTO->getCustomer());

        return $user;
    }

    /**
     * Finds a page of users.
     *
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * 
     * @return PaginationInterface The users.
     */
    public function findPage(PaginationDTO $paginationDTO, Customer $customer): PaginationInterface
    {
        return $this->userRepository->findPage(
            $paginationDTO->page,
            $paginationDTO->pageSize,
            $customer
        );
    }

    /**
     * Finds a user by their username.
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(['email' => $username]);
    }
}
