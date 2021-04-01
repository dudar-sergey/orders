<?php


namespace App\Controller\api\OrderApi;

use App\Add\Add;
use App\ebay\AllegroUserManager;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderApi
 * @package App\Controller\api\OrderApi
 * @Route("/api")
 */
class OrderApi extends AbstractController
{
    private $orderRep;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->orderRep = $em->getRepository(Order::class);
        $this->session = $session;
    }

    /**
     * @Route ("/get_orders")
     * @param Request $request
     * @return Response
     */
    public function getOrders(Request $request): Response
    {
        $word = $request->get('word') ?? null;
        $limit = $request->get('limit') ?? null;
        $offset = $request->get('offset') ?? null;
        $type = $request->get('type') ?? 'json';
        $orders = [];
        $ordersEntities = $this->orderRep->getOrders($word, $limit, $offset);
        foreach ($ordersEntities as $order) {
            $orders[] = [
              'id' => $order->getId(),
              'clientName' => $order->getBuyer(),
              'name' => $order->getAllegroOffer() ? $order->getAllegroOffer()->getProduct()->getName(): null,
              'date' => $order->getDate(),
              'status' => $order->getPayment(),
            ];
        }
        if($type == 'html') {
            return $this->render('api/tableOrders.html.twig', ['orders' => $orders]);
        }

        return new JsonResponse($orders, 200);
    }

}