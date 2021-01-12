<?php

namespace App\Entity;

use App\Repository\DescriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DescriptionRepository::class)
 */
class Description
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $enName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $enDes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plName;

    /**
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $plDes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $frDes;

    /**
     * @ORM\ManyToOne(targetEntity=ProductGroup::class)
     */
    private $productGroup;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnName(): ?string
    {
        return $this->enName;
    }

    public function setEnName(?string $enName): self
    {
        $this->enName = $enName;

        return $this;
    }

    public function getEnDes(): ?string
    {
        return $this->enDes;
    }

    public function setEnDes(?string $enDes): self
    {
        $this->enDes = $enDes;

        return $this;
    }

    public function getPlName(): ?string
    {
        return $this->plName;
    }

    public function setPlName(?string $plName): self
    {
        $this->plName = $plName;

        return $this;
    }

    public function getPlDes(): ?string
    {
        return $this->plDes;
    }

    public function setPlDes(?string $plDes): self
    {
        $this->plDes = $plDes;

        return $this;
    }

    public function getFrName(): ?string
    {
        return $this->frName;
    }

    public function setFrName(?string $frName): self
    {
        $this->frName = $frName;

        return $this;
    }

    public function getFrDes(): ?string
    {
        return $this->frDes;
    }

    public function setFrDes(?string $frDes): self
    {
        $this->frDes = $frDes;

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
}
