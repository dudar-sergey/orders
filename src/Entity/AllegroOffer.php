<?php

namespace App\Entity;

use App\Repository\AllegroOfferRepository;
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
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="allegroOffer", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allegroId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

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
}
