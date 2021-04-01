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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $allegroId;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentStatus::class, inversedBy="orders")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Sale::class, mappedBy="order")
     */
    private $sales;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="orders")
     */
    private $profile;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $originalPrice;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orders")
     */
    private $product;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="orders")
     * @ORM\JoinTable(name="order_products")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=OrderAllegroOffers::class, mappedBy="myOrder")
     */
    private $orderAllegroOffers;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->orderAllegroOffers = new ArrayCollection();
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
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
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
            $sale->setOrder($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getOrder() === $this) {
                $sale->setOrder(null);
            }
        }

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

    public function getOriginalPrice(): ?float
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?float $originalPrice): self
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|OrderAllegroOffers[]
     */
    public function getOrderAllegroOffers(): Collection
    {
        return $this->orderAllegroOffers;
    }

    public function addOrderAllegroOffer(OrderAllegroOffers $orderAllegroOffer): self
    {
        if (!$this->orderAllegroOffers->contains($orderAllegroOffer)) {
            $this->orderAllegroOffers[] = $orderAllegroOffer;
            $orderAllegroOffer->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderAllegroOffer(OrderAllegroOffers $orderAllegroOffer): self
    {
        if ($this->orderAllegroOffers->removeElement($orderAllegroOffer)) {
            // set the owning side to null (unless already changed)
            if ($orderAllegroOffer->getMyOrder() === $this) {
                $orderAllegroOffer->setMyOrder(null);
            }
        }

        return $this;
    }
}
