<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Order;
use App\Entity\OrderAllegroOffers;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderAllegroOffers|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderAllegroOffers|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderAllegroOffers[]    findAll()
 * @method OrderAllegroOffers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderAllegroOffersRepository extends ServiceEntityRepository
{
    private $em;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, OrderAllegroOffers::class);
        $this->em = $em;
    }

    public function createOrderAllegroOffer(Order $order, Product $product, int $quantity): OrderAllegroOffers
    {
        $orderAllegroOffer = new OrderAllegroOffers();
        $orderAllegroOffer
            ->setQuantity($quantity)
            ->setProduct($product)
            ->setMyOrder($order)
            ->setDate($order->getDate());
        $this->em->persist($orderAllegroOffer);
        $this->em->flush();

        return $orderAllegroOffer;
    }

    public function findByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate = null, AllegroOffer $allegroOffer = null)
    {
        if($endDate == null) {
            $endDate = new \DateTime('now');
        }
        $query = $this->createQueryBuilder('o');
        $query
            ->select('o')
            ->where('o.date >= :startDate')
            ->andWhere('o.date <= :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'));
        if($allegroOffer) {
            $query->andWhere('o.allegroOffer = '.$allegroOffer->getId());
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return OrderAllegroOffers[] Returns an array of OrderAllegroOffers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderAllegroOffers
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
