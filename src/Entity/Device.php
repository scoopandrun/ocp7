<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"device.index"})
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
     * @Groups({"device.index", "device.show"})
     */
    private ?string $model = null;

    /**
     * @ORM\Column(type="date_immutable")
     * @Groups({"device.index", "device.show"})
     */
    private ?\DateTimeImmutable $dateFirstCommercialized = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"device.index", "device.show"})
     */
    private bool $isSold = false;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_DATE"})
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

    public function getDateFirstCommercialized(): ?\DateTimeImmutable
    {
        return $this->dateFirstCommercialized;
    }

    public function setDateFirstCommercialized(\DateTimeImmutable $dateFirstCommercialized): self
    {
        $this->dateFirstCommercialized = $dateFirstCommercialized;

        return $this;
    }

    public function isSold(): ?bool
    {
        return $this->isSold;
    }

    public function setIsSold(bool $isSold): self
    {
        $this->isSold = $isSold;

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
}