<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Supply;
use App\Entity\SupplyProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SupplyProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupplyProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SupplyProduct[]    findAll()
 * @method SupplyProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplyProductRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, SupplyProduct::class);
        $this->em = $em;
    }

    public function createSupplyProduct(Supply $supply, Product $product, $quantity)
    {
        $supplyProduct = new SupplyProduct();

        $supplyProduct
            ->setQuantity($quantity ?? null)
            ->setProduct($product)
            ->setSupply($supply);
        if ($product)
        {
            $this->em->persist($supplyProduct);
            $this->em->flush();
        }
    }
}
