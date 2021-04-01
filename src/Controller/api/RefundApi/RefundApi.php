<?php


namespace App\Controller\api\RefundApi;

use App\Entity\Order;
use App\Entity\Refund;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RefundApi
 * @package App\Controller\api\RefundApi
 * @Route ("/api")
 */
class RefundApi
{
    private $refundRep;
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->refundRep = $em->getRepository(Refund::class);
    }

    /**
     * @Route ("/create_refund", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createRefund(Request $request): JsonResponse
    {
        $response = json_decode($request->getContent(), true);
        $orders = [];
        $responseApi = [];
        foreach ($response['orders'] as $order) {
            $orders[] = $this->em->getRepository(Order::class)->find($order);
        }
        foreach ($orders as $order) {
            $refund = $this->refundRep->createRefund(
                $order,
                $response['reason'],
                $response['state'],
                new \DateTime()
            );
            $responseApi['refunds'][] = [
                'date' => $refund->getCreateAt()->format('d-m-Y'),
                'buyer' => $refund->getRefundOrder()->getBuyer(),
                'productName' => $refund->getRefundOrder()->getAllegroOffer() ? $refund->getRefundOrder()->getAllegroOffer()->getProduct()->getName(): null,
            ];
        }
        $responseApi['message'] = 'Возвраты успешно добавлены';

        return new JsonResponse($responseApi);
    }
}