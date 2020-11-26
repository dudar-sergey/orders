<?php

namespace App\Repository;

use App\Entity\AllegroDeliveryMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AllegroDeliveryMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllegroDeliveryMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllegroDeliveryMethod[]    findAll()
 * @method AllegroDeliveryMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllegroDeliveryMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllegroDeliveryMethod::class);
    }

    // /**
    //  * @return AllegroDeliveryMethod[] Returns an array of AllegroDeliveryMethod objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AllegroDeliveryMethod
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
