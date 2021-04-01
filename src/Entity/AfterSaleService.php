<?php

namespace App\Entity;

use App\Repository\AfterSaleServiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AfterSaleServiceRepository::class)
 */
class AfterSaleService
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $impliedWarranty;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $returnPolicy;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, inversedBy="afterSaleService", cascade={"persist", "remove"})
     */
    private $profile;


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

    public function getImpliedWarranty(): ?string
    {
        return $this->impliedWarranty;
    }

    public function setImpliedWarranty(string $impliedWarranty): self
    {
        $this->impliedWarranty = $impliedWarranty;

        return $this;
    }

    public function getReturnPolicy(): ?string
    {
        return $this->returnPolicy;
    }

    public function setReturnPolicy(string $returnPolicy): self
    {
        $this->returnPolicy = $returnPolicy;

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

}
