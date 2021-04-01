<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sale[]    findAll()
 * @method Sale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Sale::class);
        $this->em = $em;
    }

    public function createSale(Order $order): Sale
    {
        $sale = new Sale();
        $sale
            ->setOrder($order)
            ->setCreateAt($order->getDate())
        ;
        $this->em->persist($sale);
        $this->em->flush();

        return $sale;
    }

    public function findByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate = null)
    {
        if($endDate == null) {
            $endDate = new \DateTime('now');
        }
        $query = $this->createQueryBuilder('o');
        $query
            ->select('o')
            ->where('o.createAt >= :startDate')
            ->andWhere('o.createAt <= :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'));

        return $query->getQuery()->getResult();

    }


    // /**
    //  * @return Sale[] Returns an array of Sale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sale
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
