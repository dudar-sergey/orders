<?php

namespace App\Entity;

use App\Repository\AllegroOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AllegroOfferRepository::class)
 */
class AllegroOffer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allegroId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="allegroOffers")
     */
    private $profile;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="allegroOffers")
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=OrderAllegroOffers::class, mappedBy="allegroOffer")
     */
    private $orderAllegroOffers;

    public function __construct()
    {
        $this->orderAllegroOffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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
            $orderAllegroOffer->setAllegroOffer($this);
        }

        return $this;
    }

    public function removeOrderAllegroOffer(OrderAllegroOffers $orderAllegroOffer): self
    {
        if ($this->orderAllegroOffers->removeElement($orderAllegroOffer)) {
            // set the owning side to null (unless already changed)
            if ($orderAllegroOffer->getAllegroOffer() === $this) {
                $orderAllegroOffer->setAllegroOffer(null);
            }
        }

        return $this;
    }

}
