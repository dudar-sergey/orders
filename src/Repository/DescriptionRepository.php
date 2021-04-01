<?php

namespace App\Repository;

use App\Entity\Description;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Description|null find($id, $lockMode = null, $lockVersion = null)
 * @method Description|null findOneBy(array $criteria, array $orderBy = null)
 * @method Description[]    findAll()
 * @method Description[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescriptionRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Description::class);
        $this->em = $em;
    }

    // /**
    //  * @return Description[] Returns an array of Description objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByPlName($value): ?Description
    {
        return $this->createQueryBuilder('o')
            ->where('o.plName LIKE :search')
            ->setParameter('search', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function createGroup(string $ruName): Description
    {
        $group = new Description();
        $group
            ->setRuName($ruName);
        $this->em->persist($group);
        $this->em->flush();

        return $group;
    }
}
