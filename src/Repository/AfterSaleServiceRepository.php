<?php

namespace App\Repository;

use App\Entity\AfterSaleService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AfterSaleService|null find($id, $lockMode = null, $lockVersion = null)
 * @method AfterSaleService|null findOneBy(array $criteria, array $orderBy = null)
 * @method AfterSaleService[]    findAll()
 * @method AfterSaleService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AfterSaleServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AfterSaleService::class);
    }

    // /**
    //  * @return AfterSaleService[] Returns an array of AfterSaleService objects
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
    public function findOneBySomeField($value): ?AfterSaleService
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
