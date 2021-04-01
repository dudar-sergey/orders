<?php

namespace App\Repository;

use App\Entity\OutOfStock;
use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OutOfStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfStock[]    findAll()
 * @method OutOfStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutOfStockRepository extends ServiceEntityRepository
{
    const SECONDS_IN_DAYS = 60 * 60 * 24;
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, OutOfStock::class);
        $this->em = $em;
    }

    public function getOutOfStock(Product $product)
    {
        return $this->findBy(['product' => $product->getId()]);
    }

    public function startOutOdStock(Product $product): OutOfStock
    {
        $ofs = new OutOfStock();
        $ofs
            ->setProduct($product)
            ->setStartDate(new DateTime('now'));
        $this->em->persist($ofs);
        $this->em->flush();

        return $ofs;
    }

    public function endOutOfStock(OutOfStock $ofs): OutOfStock
    {
        $ofs
            ->setEndDate(new DateTime('now'))
            ->setHandled(true);

        return $ofs;
    }

    public function getOutOfStockDays(Product $product, DateTime $startDate, DateTime $endDate)
    {
        $query = $this->createQueryBuilder('o');
        $query
            ->select('o')
            ->where('o.product = :productId')
            ->andWhere('o.endDate <= :endDate')
            ->andWhere('o.endDate <= :endDate')
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->setParameter('productId', $product->getId());
        /** @var OutOfStock[] $results */
        $results = $query->getQuery()->getResult();

        return $this->getDaysOfOutOfStockEntities($results);
    }

    /**
     * @param OutOfStock[] $entities
     * @return false|int
     */
    public function getDaysOfOutOfStockEntities($entities)
    {
        $days = 0;
        $dateNow = new DateTime('now');

        foreach ($entities as $entity) {
            if($entity->getHandled() == true) {
                $days += strtotime($entity->getEndDate()->format('Y-m-d')) - strtotime($entity->getStartDate()->format('Y-m-d'));
            } else {
                $days += strtotime($dateNow->format('Y-m-d')) - strtotime($entity->getStartDate()->format('Y-m-d'));
            }
            var_dump(floor($days / self::SECONDS_IN_DAYS));
        }

        return floor($days / self::SECONDS_IN_DAYS);
    }
}
