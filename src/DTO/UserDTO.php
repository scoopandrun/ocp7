<?php

// This DTO allows for easy validation of a user's data.
// Path: src/DTO/UserDTO.php

namespace App\DTO;

use App\Entity\Customer;
use App\Validator\UniqueEmail;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEmail()
 */
class UserDTO
{
    private ?int $id = null;

    /**
     * @Assert\NotBlank(
     *   message="Please enter an email address",
     *   groups={"create"}
     * )
     * @Assert\Email(
     *   message="Please enter a valid email address",
     *   groups={"create", "update"}
     * )
     */
    private ?string $email = null;

    /**
     * @Assert\NotBlank(
     *   message="Please enter the full name of the user",
     *   groups={"create"}
     * )
     */
    private ?string $fullname = null;

    /**
     * @Assert\NotBlank(
     *   message="Please enter a password",
     *   groups={"create"}
     * )
     * @Assert\NotCompromisedPassword()
     * @Assert\Length(
     *   min=10,
     *   minMessage="Your password must be at least {{ limit }} characters long"
     * )
     */
    private ?string $password = null;

    private ?Customer $customer = null;

    public function __construct(
        ?string $email = null,
        ?string $fullname = null,
        ?string $password = null,
        ?Customer $customer = null
    ) {
        $this->email = $email;
        $this->fullname = $fullname;
        $this->password = $password;
        $this->customer = $customer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }
}
