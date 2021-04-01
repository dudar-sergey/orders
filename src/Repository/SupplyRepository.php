<?php

namespace App\Repository;

use App\Entity\Supply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Supply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supply[]    findAll()
 * @method Supply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplyRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Supply::class);
        $this->em = $em;
    }

    public function createSupply($sender, $date, $recipient, $contract): Supply
    {
        $supply = new Supply();
        $supply
            ->setContract($contract)
            ->setDate($date)
            ->setRecipient($recipient)
            ->setSender($sender);
        $this->em->persist($supply);
        $this->em->flush();

        return $supply;
    }

    public function getLastSupply()
    {
        $query = $this->createQueryBuilder('o');
        $query->select('o')
            ->orderBy('o.date', 'DESC');
        return $query->getQuery()->getResult()[0];
    }
}
