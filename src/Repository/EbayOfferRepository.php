<?php

namespace App\Repository;

use App\Entity\EbayOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EbayOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method EbayOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method EbayOffer[]    findAll()
 * @method EbayOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EbayOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EbayOffer::class);
    }

    // /**
    //  * @return EbayOffer[] Returns an array of EbayOffer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EbayOffer
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
