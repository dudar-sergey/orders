<?php

namespace App\Repository;

use App\Controller\user\UserController;
use App\Entity\AllegroOffer;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Profile;
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
     * @param Profile|null $profile
     * @return Order
     */
    public function createOrder($data, Profile $profile = null): Order
    {
        $order = new Order();
        $price = $data['price'];
        $buyer = $data['buyer'];
        $payment = $data['payment'] ?? null;
        $date = $data['date'];
        $placement = $data['placement'];
        $allegroId = $data['allegroId'] ?? null;
        $product = $data['product'] ?? null;
        $order
            ->setPrice($price)
            ->setBuyer($buyer)
            ->setPayment($payment)
            ->setDate($date)
            ->setPlacement($placement)
            ->setAllegroId($allegroId)
            ->setProfile($profile)
            ->setProduct($product)
        ;
        $this->em->persist($order);
        $this->em->flush();
        return $order;
    }

    public function updateOrder(Order $order, string $status) {
        $order
            ->setPayment($status);
        $this->em->flush();
    }

    public function findAllByDate()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.date', 'desc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getOrders(string $word = null, int $limit = null, int $offset = null)
    {
        $query = $this->createQueryBuilder('o');
        if(!$word) {
            return $this->findBy([], [], $limit, $offset);
        } else {
            $query
                ->where('o.buyer LIKE :word')
                ->orWhere('o.allegroId LIKE :word')
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->setParameter('word', '%'.$word.'%');
        }

        return $query->getQuery()->getResult();
    }

    public function findByDate(\DateTimeInterface $startDate, \DateTimeInterface $endDate = null)
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
        return $query->getQuery()->getResult();
    }
}
