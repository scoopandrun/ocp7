<?php

// Users are employees of BileMo's customers (Customer entity).
// They can log into the API with their username and password.
// Path: src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "user.show",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "user.index", "user.show" })
 * )
 * 
 * @Hateoas\Relation(
 *   "update",
 *   href = @Hateoas\Route(
 *     "user.update",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "user.show" })
 * )
 * 
 * @Hateoas\Relation(
 *   "delete",
 *   href = @Hateoas\Route(
 *     "user.delete",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "user.show" })
 * )
 * 
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user.index", "user.show"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user.index", "user.show", "user.create", "user.update"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.index", "user.show", "user.create", "user.update"})
     */
    private $fullname;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user.show"})
     */
    private ?Customer $customer = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }
}
