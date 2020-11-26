<?php

namespace App\Entity;

use App\Repository\EbayOfferRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EbayOfferRepository::class)
 */
class EbayOffer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="ebayOffer", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $eBayId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEBayId(): ?string
    {
        return $this->eBayId;
    }

    public function setEBayId(?string $eBayId): self
    {
        $this->eBayId = $eBayId;

        return $this;
    }
}
