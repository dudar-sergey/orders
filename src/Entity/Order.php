<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000 ,nullable=true)
     */
    private $eId;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $buyer;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     */
    private $ProductId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $payment;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    public function __construct()
    {
        $this->ProductId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEId(): ?string
    {
        return $this->eId;
    }

    public function setEId(?string $eId): self
    {
        $this->eId = $eId;

        return $this;
    }

    public function getBuyer(): ?string
    {
        return $this->buyer;
    }

    public function setBuyer(?string $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->ProductId;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->ProductId->contains($product)) {
            $this->ProductId[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->ProductId->contains($product)) {
            $this->ProductId->removeElement($product);
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
