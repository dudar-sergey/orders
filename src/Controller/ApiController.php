<?php

namespace App\Controller;

use App\Add\Add;
use App\api\Api;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    private $em;
    private $add;

    public function __construct(Api $api, SessionInterface $session, EntityManagerInterface $em, Add $add)
    {
        $this->api = $api;
        $this->session = $session;
        $this->em = $em;
        $this->add = $add;
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

    /**
     * @Route("/get_products_html")
     * @param Request $request
     * @return Response
     */
    public function getProductsHtml(Request $request): Response
    {
        $word = $request->get('word') ?? null;
        $offset = $limit = $this->session->get('limit') ?? 10;
        $page = $request->get('p') ?? 1;
        if($page == 1 || $page == 0 || $page == null)
        {
            $offset = 0;
            $page = 1;
        }

        $products = $this->em->getRepository(Product::class)->findByArticleAndName($word, $word, $limit, $offset*($page-1));
        $pages = ceil(count($this->em->getRepository(Product::class)->findByArticleAndName($word, $word))/$limit);
        return $this->render('api/tableProducts.html.twig', [
            'products'=>$products,
            'currentPage' => $page,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/products_for_select")
     * @param Request $request
     * @return JsonResponse
     */
    public function getProductsForSelect(Request $request): JsonResponse
    {
        $word = $request->get('word') ?? null;
        $products = $this->em->getRepository(Product::class)->findByArticleAndName($word);
        $response = [];

        foreach ($products as $product) {
            $response[] = [
              'name' => $product->getName(),
              'id' => $product->getId()
            ];
        }
        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/create_kit", methods={"POST"})
     * @param Request $request
     */
    public function createKit(Request $request)
    {
        $products = [];
        $errors = [];
        $content = json_decode($request->getContent(), true);
        $options = $content['options'];
        if(!$content['products']) $errors['message'] = 'Не найдено ничего';
        foreach ($content['products'] as $item) {
            $product =  $this->em->getRepository(Product::class)->find($item);
            if($product) {
                $products[] = $product;
            } else {
                $errors['message'] = 'Не найден id: '.$item;
            }
        }

        $this->add->addKitProduct($products, $options);

        if(empty($errors)) {
            return new JsonResponse(['message' => 'Комплект создан']);
        } else {
            return new JsonResponse($errors, 200);
        }
    }
}