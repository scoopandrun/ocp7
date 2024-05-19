<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;


/**
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "device.show",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "device.index", "device.show" })
 * )
 * 
 * @Hateoas\Relation(
 *   "collection",
 *   href = @Hateoas\Route("device.index"),
 *   exclusion = @Hateoas\Exclusion(groups = { "device.show" })
 * )
 * 
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"device.index", "brand.show", "brand.devices"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"device.index", "device.show"})
     */
    private ?Brand $brand = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"device.index", "device.show", "brand.show", "brand.devices"})
     */
    private ?string $model = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"device.index", "device.show", "brand.show", "brand.devices"})
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string")
     * @Groups({"device.show"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
