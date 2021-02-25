<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AllegroOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllegroOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllegroOffer[]    findAll()
 * @method AllegroOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllegroOfferRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, AllegroOffer::class);
        $this->em = $em;
    }

    public function createOffer($id, Product $product): AllegroOffer
    {
        $offer = new AllegroOffer();
        $offer
            ->setProduct($product)
            ->setAllegroId($id)
            ->setStatus(false)
        ;
        $this->em->persist($offer);
        $this->em->flush();
        return $offer;
    }

    // /**
    //  * @return AllegroOffer[] Returns an array of AllegroOffer objects
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
    public function findOneBySomeField($value): ?AllegroOffer
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
