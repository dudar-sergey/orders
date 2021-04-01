<?php

namespace App\Entity;

use App\Repository\DeliveryCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryCategoryRepository::class)
 */
class DeliveryCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=AllegroDeliveryMethod::class, mappedBy="category")
     */
    private $allegroDeliveryMethods;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $allegroCategoryId;

    public function __construct()
    {
        $this->allegroDeliveryMethods = new ArrayCollection();
        $this->products = new ArrayCollection();
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

    /**
     * @return Collection|AllegroDeliveryMethod[]
     */
    public function getAllegroDeliveryMethods(): Collection
    {
        return $this->allegroDeliveryMethods;
    }

    public function addAllegroDeliveryMethod(AllegroDeliveryMethod $allegroDeliveryMethod): self
    {
        if (!$this->allegroDeliveryMethods->contains($allegroDeliveryMethod)) {
            $this->allegroDeliveryMethods[] = $allegroDeliveryMethod;
            $allegroDeliveryMethod->setCategory($this);
        }

        return $this;
    }

    public function removeAllegroDeliveryMethod(AllegroDeliveryMethod $allegroDeliveryMethod): self
    {
        if ($this->allegroDeliveryMethods->removeElement($allegroDeliveryMethod)) {
            // set the owning side to null (unless already changed)
            if ($allegroDeliveryMethod->getCategory() === $this) {
                $allegroDeliveryMethod->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    public function getAllegroCategoryId(): ?string
    {
        return $this->allegroCategoryId;
    }

    public function setAllegroCategoryId(?string $allegroCategoryId): self
    {
        $this->allegroCategoryId = $allegroCategoryId;

        return $this;
    }
}
