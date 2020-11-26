<?php

namespace App\Controller;

use App\api\Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{
    private $api;


    public function __construct(Api $api)
    {
        $this->api = $api;
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
    public function getProducts(Request $request)
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
}