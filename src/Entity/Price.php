<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PriceRepository::class)
 */
class Price
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="prices", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pl;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $de;

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

    public function getPl(): ?float
    {
        return $this->pl;
    }

    public function setPl(?float $pl): self
    {
        $this->pl = $pl;

        return $this;
    }

    public function getDe(): ?float
    {
        return $this->de;
    }

    public function setDe(?float $de): self
    {
        $this->de = $de;

        return $this;
    }
}
