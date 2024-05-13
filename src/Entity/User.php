<?php

// Users are employees of BileMo's customers (Customer entity).
// They can log into the API with their username and password.
// Path: src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
 *   "delete",
 *   href = @Hateoas\Route(
 *     "user.delete",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "user.show" })
 * )
 * 
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
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
     * @Groups({"user.index", "user.show", "user.create"})
     * @Assert\NotBlank(message="Please enter an email address")
     * @Assert\Email(message="Please enter a valid email address")
     */
    private ?string $email = null;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user.show"})
     */
    private ?Customer $company = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user.create"})
     */
    private ?string $password = null;

    /**
     * @Assert\NotCompromisedPassword
     * @Assert\Length(
     *   min=10,
     *   minMessage="Your password must be at least {{ limit }} characters long"
     * )
     */
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

    public function getCompany(): ?Customer
    {
        return $this->company;
    }

    public function setCompany(?Customer $company): self
    {
        $this->company = $company;

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

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
