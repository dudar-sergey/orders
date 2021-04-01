<?php

namespace App\Entity;

use App\Repository\ProductDeliveryMethodRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductDeliveryMethodRepository::class)
 */
class ProductDeliveryMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="yes")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=AllegroDeliveryMethod::class, inversedBy="productDeliveryMethods")
     */
    private $deliveryMethod;

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

    public function getDeliveryMethod(): ?AllegroDeliveryMethod
    {
        return $this->deliveryMethod;
    }

    public function setDeliveryMethod(?AllegroDeliveryMethod $deliveryMethod): self
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }
}
