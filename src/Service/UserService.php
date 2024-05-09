<?php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Finds a page of users.
     *
     * @param PaginationDTO $paginationDTO The pagination data transfer object.
     * 
     * @return Paginator The users.
     */
    public function findPage(PaginationDTO $paginationDTO, Customer $company): Paginator
    {
        return $this->userRepository->findPage(
            $paginationDTO->page,
            $paginationDTO->pageSize,
            $company
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
