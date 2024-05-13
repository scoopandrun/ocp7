<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;

/**
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "brand.show",
 *     parameters = { "id" = "expr(object.getId())" }
 *   ),
 *   exclusion = @Hateoas\Exclusion(groups = { "brand.index", "brand.show" })
 * )
 * 
 * @ORM\Entity(repositoryClass=BrandRepository::class)
 */
class Brand implements \Stringable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"brand.index", "brand.show", "device.index", "device.show"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brand.index", "brand.show", "device.index", "device.show"})
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity=Device::class, mappedBy="brand", orphanRemoval=true)
     * @Groups({"brand.show"})
     */
    private Collection $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setBrand($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getBrand() === $this) {
                $device->setBrand(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"brand.index", "brand.show"})
     */
    public function getDeviceCount(): int
    {
        return $this->getDevices()->count();
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
