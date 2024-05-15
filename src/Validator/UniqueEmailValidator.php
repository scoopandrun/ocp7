<?php

namespace App\Validator;

use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($userDTO, Constraint $constraint): void
    {
        if (!$userDTO instanceof UserDTO) {
            throw new UnexpectedValueException($userDTO, UserDTO::class);
        }

        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedValueException($constraint, UniqueEmail::class);
        }

        if (!$userDTO->getEmail()) {
            return;
        }

        $emailAlreadyTaken = $this->entityManager
            ->getRepository(User::class)
            ->checkEmailAlreadyTaken($userDTO->getEmail(), $userDTO->getId());

        if (!$emailAlreadyTaken) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $userDTO->getEmail())
            ->atPath('email')
            ->addViolation();
    }
}
