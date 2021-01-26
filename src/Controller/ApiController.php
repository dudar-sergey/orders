<?php

namespace App\Controller;

use App\api\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{
    private $api;
    private $session;

    public function __construct(Api $api, SessionInterface $session)
    {
        $this->api = $api;
        $this->session = $session;
    }

    /**
     * @Route("/getorders", name="getOrders")
     */
    public function getOrders()
    {
        return $this->api->getOrders();
    }

    /**
     * @Route("/getProducts", name="getProducts", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getProducts(Request $request): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        return $this->api->getProducts($request->get('filters'));
    }

    public function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if($data == null)
        {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @Route("/change_order_status", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changeOrderStatus(Request $request): JsonResponse
    {
        $data = $this->transformJsonBody($request);

        return $this->api->changeOrderStatus($data->get('orderId'), $data->get('paymentStatusId'));
    }

    /**
     * @Route ("/change_limit_for_products")
     * @param Request $request
     * @return JsonResponse
     */
    public function setLimitForProducts(Request $request): JsonResponse
    {
        $limit = $request->get('limit') ?? 10;
        if($limit > 100)
        {
            $limit = 10;
        }
        $this->session->set('limit', $limit);

        return $this->api->response('ok');
    }
}