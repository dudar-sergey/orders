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
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $upc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sync;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $eId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $url;

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
     * @ORM\ManyToOne(targetEntity=ProductGroup::class)
     */
    private $productGroup;

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
     * @ORM\OneToOne(targetEntity=AllegroOffer::class, mappedBy="product", cascade={"persist", "remove"})
     */
    private $allegroOffer;

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

    public function getSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): self
    {
        $this->sync = $sync;

        return $this;
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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

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

    public function getProductGroup(): ?ProductGroup
    {
        return $this->productGroup;
    }

    public function setProductGroup(?ProductGroup $productGroup): self
    {
        $this->productGroup = $productGroup;

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

    public function getAllegroOffer(): ?AllegroOffer
    {
        return $this->allegroOffer;
    }

    public function setAllegroOffer(?AllegroOffer $allegroOffer): self
    {
        $this->allegroOffer = $allegroOffer;

        // set (or unset) the owning side of the relation if necessary
        $newProduct = null === $allegroOffer ? null : $this;
        if ($allegroOffer->getProduct() !== $newProduct) {
            $allegroOffer->setProduct($newProduct);
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
}
