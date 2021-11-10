<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $upc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $articul;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     */
    private $analogs;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $auto;

    /**
     * @ORM\ManyToOne(targetEntity=Description::class)
     */
    private $des;

    /**
     * @ORM\OneToMany(targetEntity=Sale::class, mappedBy="product")
     */
    private $sales;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $kit;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     * @ORM\JoinTable(name="kitProduct",
     *      joinColumns={@ORM\JoinColumn(name="product", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="kitProduct", referencedColumnName="id")}
     * )
     */
    private $kitProducts;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="product")
     */
    private $images;

    /**
     * @ORM\OneToOne(targetEntity=EbayOffer::class, mappedBy="product", cascade={"persist", "remove"})
     */
    private $ebayOffer;

    /**
     * @ORM\OneToMany(targetEntity=SupplyProduct::class, mappedBy="product")
     */
    private $supplyProducts;

    /**
     * @ORM\OneToOne(targetEntity=Price::class, mappedBy="product", cascade={"persist", "remove"})
     */
    private $prices;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $allegroTitle;

    /**
     * @ORM\OneToMany(targetEntity=AllegroOffer::class, mappedBy="product")
     */
    private $allegroOffers;

    /**
     * @ORM\OneToMany(targetEntity=ProductDeliveryMethod::class, mappedBy="product")
     */
    private $productDeliveryMethod;

    /**
     * @ORM\ManyToOne(targetEntity=DeliveryCategory::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="products")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=OutOfStock::class, mappedBy="product")
     */
    private $outOfStocks;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $allegroProductId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $allegroImages = [];


    public function __toString(): string
    {
        return $this->getArticul().' '.$this->getName();
    }

    public function __construct()
    {
        $this->analogs = new ArrayCollection();
        $this->kitProducts = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->supplyProducts = new ArrayCollection();
        $this->allegroOffers = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->outOfStocks = new ArrayCollection();
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUpc(): ?string
    {
        return $this->upc;
    }

    public function setUpc(?string $upc): self
    {
        $this->upc = $upc;

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

    public function getArticul(): ?string
    {
        return $this->articul;
    }

    public function setArticul(?string $articul): self
    {
        $this->articul = $articul;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAnalogs(): Collection
    {
        return $this->analogs;
    }

    public function addAnalog(self $analog): self
    {
        if (!$this->analogs->contains($analog)) {
            $this->analogs[] = $analog;
        }

        return $this;
    }

    public function removeAnalog(self $analog): self
    {
        if ($this->analogs->contains($analog)) {
            $this->analogs->removeElement($analog);
        }

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getAuto(): ?string
    {
        return $this->auto;
    }

    public function setAuto(?string $auto): self
    {
        $this->auto = $auto;

        return $this;
    }

    public function getDes(): ?Description
    {
        return $this->des;
    }

    public function setDes(?Description $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getSales()
    {
        return $this->sales;
    }

    public function getKit(): ?bool
    {
        return $this->kit;
    }

    public function setKit(?bool $kit): self
    {
        $this->kit = $kit;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getKitProducts(): Collection
    {
        return $this->kitProducts;
    }

    public function addKitProduct(self $kitProduct): self
    {
        if (!$this->kitProducts->contains($kitProduct)) {
            $this->kitProducts[] = $kitProduct;
        }

        return $this;
    }

    public function removeKitProduct(self $kitProduct): self
    {
        if ($this->kitProducts->contains($kitProduct)) {
            $this->kitProducts->removeElement($kitProduct);
        }

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getEbayOffer(): ?EbayOffer
    {
        return $this->ebayOffer;
    }

    public function setEbayOffer(?EbayOffer $ebayOffer): self
    {
        $this->ebayOffer = $ebayOffer;

        // set (or unset) the owning side of the relation if necessary
        $newProduct = null === $ebayOffer ? null : $this;
        if ($ebayOffer->getProduct() !== $newProduct) {
            $ebayOffer->setProduct($newProduct);
        }

        return $this;
    }

    /**
     * @return Collection|SupplyProduct[]
     */
    public function getSupplyProducts(): Collection
    {
        return $this->supplyProducts;
    }

    public function addSupplyProduct(SupplyProduct $supplyProduct): self
    {
        if (!$this->supplyProducts->contains($supplyProduct)) {
            $this->supplyProducts[] = $supplyProduct;
            $supplyProduct->setProduct($this);
        }

        return $this;
    }

    public function removeSupplyProduct(SupplyProduct $supplyProduct): self
    {
        if ($this->supplyProducts->removeElement($supplyProduct)) {
            // set the owning side to null (unless already changed)
            if ($supplyProduct->getProduct() === $this) {
                $supplyProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrices(): ?Price
    {
        return $this->prices;
    }

    public function setPrices(?Price $prices): self
    {
        $this->prices = $prices;

        // set (or unset) the owning side of the relation if necessary
        $newProduct = null === $prices ? null : $this;
        if ($prices->getProduct() !== $newProduct) {
            $prices->setProduct($newProduct);
        }

        return $this;
    }

    public function addQuantity($quantity): self
    {
        $this->quantity += $quantity;

        return $this;
    }

    public function getAllegroTitle(): ?string
    {
        return $this->allegroTitle;
    }

    public function setAllegroTitle(?string $allegroTitle): self
    {
        $this->allegroTitle = $allegroTitle;

        return $this;
    }

    public function getAllegroOffer($profile)
    {
        foreach ($this->allegroOffers as $allegroOffer) {
            if($profile->getId() == $allegroOffer->getProfile()->getId() && $allegroOffer->getStatus() !== null) {
                return $allegroOffer;
            }
        }
        return null;
    }

    public function getAllegroOffers()
    {
        return $this->allegroOffers;
    }

    public function addAllegroOffer(AllegroOffer $allegroOffer): self
    {
        if (!$this->allegroOffers->contains($allegroOffer)) {
            $this->allegroOffers[] = $allegroOffer;
            $allegroOffer->setProduct($this);
        }

        return $this;
    }

    public function removeAllegroOffer(AllegroOffer $allegroOffer): self
    {
        if ($this->allegroOffers->removeElement($allegroOffer)) {
            // set the owning side to null (unless already changed)
            if ($allegroOffer->getProduct() === $this) {
                $allegroOffer->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductDeliveryMethod[]
     */
    public function getProductDeliveryMethods(): Collection
    {
        return $this->productDeliveryMethod;
    }

    public function getDeliveryMethod(Profile $profile)
    {
        if(!$this->category)
            return null;
        foreach ($this->category->getAllegroDeliveryMethods() as $deliveryMethod) {
            if($profile->getId() == $deliveryMethod->getProfile()->getId()) {
                return $deliveryMethod;
            }
        }
        return null;
    }

    public function addProductDeliveryMethod(ProductDeliveryMethod $productDeliveryMethod): self
    {
        if (!$this->productDeliveryMethod->contains($productDeliveryMethod)) {
            $this->productDeliveryMethod[] = $productDeliveryMethod;
            $productDeliveryMethod->setProduct($this);
        }

        return $this;
    }

    public function removeProductDeliveryMethod(ProductDeliveryMethod $productDeliveryMethod): self
    {
        if ($this->productDeliveryMethod->removeElement($productDeliveryMethod)) {
            // set the owning side to null (unless already changed)
            if ($productDeliveryMethod->getProduct() === $this) {
                $productDeliveryMethod->setProduct(null);
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

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|OutOfStock[]
     */
    public function getOutOfStocks(): Collection
    {
        return $this->outOfStocks;
    }

    public function addOutOfStock(OutOfStock $outOfStock): self
    {
        if (!$this->outOfStocks->contains($outOfStock)) {
            $this->outOfStocks[] = $outOfStock;
            $outOfStock->setProduct($this);
        }

        return $this;
    }

    public function removeOutOfStock(OutOfStock $outOfStock): self
    {
        if ($this->outOfStocks->removeElement($outOfStock)) {
            // set the owning side to null (unless already changed)
            if ($outOfStock->getProduct() === $this) {
                $outOfStock->setProduct(null);
            }
        }

        return $this;
    }

    public function getAllegroProductId(): ?string
    {
        return $this->allegroProductId;
    }

    public function setAllegroProductId(?string $allegroProductId): self
    {
        $this->allegroProductId = $allegroProductId;

        return $this;
    }

    public function getAllegroImages(): ?array
    {
        return $this->allegroImages;
    }

    public function setAllegroImages(?array $allegroImages): self
    {
        $this->allegroImages = $allegroImages;

        return $this;
    }

    public function hasImages()
    {
        return !$this->images->isEmpty() || !empty($this->allegroImages);
    }

}
