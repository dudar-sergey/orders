<?php

namespace App\Repository;

use App\Entity\Progress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Progress|null find($id, $lockMode = null, $lockVersion = null)
 * @method Progress|null findOneBy(array $criteria, array $orderBy = null)
 * @method Progress[]    findAll()
 * @method Progress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgressRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Progress::class);
        $this->em = $em;
    }

    public function createProgress($processId, $percent)
    {
        $progress = new Progress();
        $progress
            ->setPercent($percent)
            ->setProcessId($processId);
        $this->em->persist($progress);
        $this->em->flush();
    }

    public function updateProgress($processId, $percent, $message)
    {
        $progress = $this->findOneBy(['processId' => $processId]);
        $progress
            ->setPercent($percent)
            ->setMessage($message);
        $this->em->flush();

    }
}
