<?php

namespace App\Entity;

use App\Repository\SupplyRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SupplyRepository::class)
 */
class Supply
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
    private $sender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $recipient;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sync;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $contract;

    /**
     * @ORM\OneToMany(targetEntity=SupplyProduct::class, mappedBy="supply")
     */
    private $supplyProducts;

    public function __construct()
    {
        $this->supplyProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): self
    {
        $this->recipient = $recipient;

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

    public function getContract(): ?string
    {
        return $this->contract;
    }

    public function setContract(?string $contract): self
    {
        $this->contract = $contract;

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
            $supplyProduct->setSupply($this);
        }

        return $this;
    }

    public function removeSupplyProduct(SupplyProduct $supplyProduct): self
    {
        if ($this->supplyProducts->removeElement($supplyProduct)) {
            // set the owning side to null (unless already changed)
            if ($supplyProduct->getSupply() === $this) {
                $supplyProduct->setSupply(null);
            }
        }

        return $this;
    }
}
