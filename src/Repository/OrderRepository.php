<?php

namespace App\Repository;

use App\Entity\AllegroOffer;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Order::class);
        $this->em = $em;
    }

    /**
     * @param $data
     * - allegroOfferId
     * - price
     * - buyer
     * - payment
     * - Date date
     * - placement
     * - allegroId
     * @return Order
     */
    public function createUpdateOrder($data): Order
    {
        /** @var AllegroOffer $allegroOffer */
        $allegroOffer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $data['allegroOfferId']]);
        $price = $data['price'];
        $buyer = $data['buyer'];
        $payment = $data['payment'];
        $date = $data['date'];
        $placement = $data['placement'];
        $allegroId = $data['allegroId'];
        $order = $this->findOneBy(['allegroId' => $allegroId]);
        if(!$order)
            $order = new Order();
        $order
            ->setAllegroOffer($allegroOffer)
            ->setPrice($price)
            ->setBuyer($buyer)
            ->setPayment($payment)
            ->setDate($date)
            ->setPlacement($placement)
            ->setAllegroId($allegroId)
        ;
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    public function findAllByDate()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.date', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }
}
