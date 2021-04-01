<?php

namespace App\Entity;

use App\Repository\OrderAllegroOffersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderAllegroOffersRepository::class)
 */
class OrderAllegroOffers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderAllegroOffers")
     */
    private $myOrder;

    /**
     * @ORM\ManyToOne(targetEntity=AllegroOffer::class, inversedBy="orderAllegroOffers")
     */
    private $allegroOffer;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyOrder(): ?Order
    {
        return $this->myOrder;
    }

    public function setMyOrder(?Order $myOrder): self
    {
        $this->myOrder = $myOrder;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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
}
