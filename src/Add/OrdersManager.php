<?php


namespace App\Add;


use App\ebay\AllegroUserManager;
use App\ebay\Ebay;
use App\Entity\AllegroOffer;
use App\Entity\OrderAllegroOffers;
use App\Entity\Sale;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;

class OrdersManager extends Manager
{
    private $ebayManager;
    private $ppm;

    public function __construct(EntityManagerInterface $em, AllegroUserManager $am, Ebay $ebayManager, ProductPlacementManager $ppm)
    {
        parent::__construct($em, $am);
        $this->ppm = $ppm;
        $this->ebayManager = $ebayManager;
    }

    public function syncOrdersFromAllegro(Profile $profile): array
    {
        $response = json_decode($this->am->getOrdersFromAllegro($profile), true);
        $newOrders = [];
        $allegroOrders = $response['checkoutForms'];
        foreach ($allegroOrders as $order) {
            $currentOrder = $this->orderRep->findOneBy(['allegroId' => $order['id']]);
            if($currentOrder) {
                $this->orderRep->updateOrder($currentOrder, $order['status']);
            } else {

                $newOrders[] = $this->createAllegroOrder($order, $profile);
            }
        }
        return $newOrders;
    }

    public function createAllegroOrder($order, $profile): ?int
    {
        $newOrder = $this->orderRep->createOrder([
            'price' => $order['payment']['paidAmount']['amount'],
            'buyer' => $order['buyer']['firstName'].' '.$order['buyer']['lastName'].' '.$order['buyer']['login'],
            'placement' => 'allegro',
            'payment' => $order['status'],
            'date' => new DateTime($order['updatedAt']),
            'allegroId' => $order['id']
        ],$profile);
        foreach ($order['lineItems'] as $offer) {
            $allegroOffer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $offer['offer']['id']]);
            if($allegroOffer) {
                $this->em->getRepository(OrderAllegroOffers::class)->createOrderAllegroOffer($newOrder, $allegroOffer, $offer['quantity']);
            }
            $quantity = $allegroOffer->getProduct()->getQuantity() - $offer['quantity'];
            $this->ppm->changeQuantityProduct($allegroOffer->getProduct(), $quantity, $profile);
        }
        $this->em->getRepository(Sale::class)->createSale($newOrder);

        return $newOrder->getId();
    }
}