<?php

namespace App\Entity;

use App\Repository\AllegroDeliveryMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AllegroDeliveryMethodRepository::class)
 */
class AllegroDeliveryMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $methodId;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="allegroDeliveryMethods")
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity=ProductDeliveryMethod::class, mappedBy="deliveryMethod")
     */
    private $productDeliveryMethods;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryCategory::class, inversedBy="allegroDeliveryMethods")
     */
    private $category;

    public function __construct()
    {
        $this->productDeliveryMethods = new ArrayCollection();
    }


    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMethodId(): ?string
    {
        return $this->methodId;
    }

    public function setMethodId(?string $methodId): self
    {
        $this->methodId = $methodId;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection|ProductDeliveryMethod[]
     */
    public function getProductDeliveryMethods(): Collection
    {
        return $this->productDeliveryMethods;
    }

    public function addProductDeliveryMethod(ProductDeliveryMethod $productDeliveryMethod): self
    {
        if (!$this->productDeliveryMethods->contains($productDeliveryMethod)) {
            $this->productDeliveryMethods[] = $productDeliveryMethod;
            $productDeliveryMethod->setDeliveryMethod($this);
        }

        return $this;
    }

    public function removeProductDeliveryMethod(ProductDeliveryMethod $productDeliveryMethod): self
    {
        if ($this->productDeliveryMethods->removeElement($productDeliveryMethod)) {
            // set the owning side to null (unless already changed)
            if ($productDeliveryMethod->getDeliveryMethod() === $this) {
                $productDeliveryMethod->setDeliveryMethod(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?DeliveryCategory
    {
        return $this->category;
    }

    public function setCategory(?DeliveryCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

}
