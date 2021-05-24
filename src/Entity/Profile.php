<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
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
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $clientSecret;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="profiles")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="profile")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=AllegroOffer::class, mappedBy="profile")
     */
    private $allegroOffers;

    /**
     * @ORM\OneToMany(targetEntity=AllegroDeliveryMethod::class, mappedBy="profile")
     */
    private $allegroDeliveryMethods;

    /**
     * @ORM\OneToOne(targetEntity=AfterSaleService::class, mappedBy="profile", cascade={"persist", "remove"})
     */
    private $afterSaleService;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $allegroAccessToken;

    /**
     * @ORM\Column(type="string", length=2500, nullable=true)
     */
    private $allegroRefreshToken;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="profile")
     */
    private $images;

    public function __toString()
    {
        return $this->username;
    }

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->allegroOffers = new ArrayCollection();
        $this->allegroDeliveryMethods = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setAllegroClientSecret(?string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

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
            $order->setProfile($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getProfile() === $this) {
                $order->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AllegroOffer[]
     */
    public function getAllegroOffers(): Collection
    {
        return $this->allegroOffers;
    }

    public function addAllegroOffer(AllegroOffer $allegroOffer): self
    {
        if (!$this->allegroOffers->contains($allegroOffer)) {
            $this->allegroOffers[] = $allegroOffer;
            $allegroOffer->setProfile($this);
        }

        return $this;
    }

    public function removeAllegroOffer(AllegroOffer $allegroOffer): self
    {
        if ($this->allegroOffers->removeElement($allegroOffer)) {
            // set the owning side to null (unless already changed)
            if ($allegroOffer->getProfile() === $this) {
                $allegroOffer->setProfile(null);
            }
        }

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
            $allegroDeliveryMethod->setProfile($this);
        }

        return $this;
    }

    public function removeAllegroDeliveryMethod(AllegroDeliveryMethod $allegroDeliveryMethod): self
    {
        if ($this->allegroDeliveryMethods->removeElement($allegroDeliveryMethod)) {
            // set the owning side to null (unless already changed)
            if ($allegroDeliveryMethod->getProfile() === $this) {
                $allegroDeliveryMethod->setProfile(null);
            }
        }

        return $this;
    }

    public function getAfterSaleService(): ?AfterSaleService
    {
        return $this->afterSaleService;
    }

    public function setAfterSaleService(?AfterSaleService $afterSaleService): self
    {
        $this->afterSaleService = $afterSaleService;

        // set (or unset) the owning side of the relation if necessary
        $newProfile = null === $afterSaleService ? null : $this;
        if ($afterSaleService->getProfile() !== $newProfile) {
            $afterSaleService->setProfile($newProfile);
        }

        return $this;
    }

    public function getAllegroAccessToken(): ?string
    {
        return $this->allegroAccessToken;
    }

    public function setAllegroAccessToken(?string $allegroAccessToken): self
    {
        $this->allegroAccessToken = $allegroAccessToken;

        return $this;
    }

    public function getAllegroRefreshToken(): ?string
    {
        return $this->allegroRefreshToken;
    }

    public function setAllegroRefreshToken(?string $allegroRefreshToken): self
    {
        $this->allegroRefreshToken = $allegroRefreshToken;

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
            $image->setProfile($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProfile() === $this) {
                $image->setProfile(null);
            }
        }

        return $this;
    }
}
