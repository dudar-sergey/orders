<?php

namespace App\Repository;

use App\Controller\user\UserController;
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
    private $uc;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, UserController $uc)
    {
        parent::__construct($registry, Order::class);
        $this->em = $em;
        $this->uc = $uc;
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
    public function createUpdateOrder($data, $user): Order
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
            ->setUser($user);
        ;
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    public function findAllByDate($user)
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('o.date', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }
}
