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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $placement;

    /**
     * @ORM\ManyToOne(targetEntity=AllegroOffer::class, inversedBy="orders")
     */
    private $allegroOffer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $allegroId;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentStatus::class, inversedBy="orders")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Sale::class, mappedBy="appOrder")
     */
    private $sales;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    public function __construct()
    {
        $this->ProductId = new ArrayCollection();
        $this->sales = new ArrayCollection();
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

    public function getPlacement(): ?string
    {
        return $this->placement;
    }

    public function setPlacement(?string $placement): self
    {
        $this->placement = $placement;

        return $this;
    }

    public function getAllegroOffer(): ?AllegroOffer
    {
        return $this->allegroOffer;
    }

    public function setAllegroOffer(?AllegroOffer $allegroOffer): self
    {
        $this->allegroOffer = $allegroOffer;

        return $this;
    }

    public function getAllegroId(): ?string
    {
        return $this->allegroId;
    }

    public function setAllegroId(?string $allegroId): self
    {
        $this->allegroId = $allegroId;

        return $this;
    }

    public function getStatus(): ?PaymentStatus
    {
        return $this->status;
    }

    public function setStatus(?PaymentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Sale[]
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setAppOrder($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getAppOrder() === $this) {
                $sale->setAppOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
