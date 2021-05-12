<?php

namespace App\Controller;

use App\Add\Add;
use App\api\Api;
use App\Controller\user\UserController;
use App\ebay\AllegroUserManager;
use App\Entity\AllegroDeliveryMethod;
use App\Entity\AllegroOffer;
use App\Entity\Product;
use App\Entity\Profile;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

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
    private $am;

    public function __construct(Api $api, SessionInterface $session, EntityManagerInterface $em, Add $add, AllegroUserManager $am)
    {
        $this->api = $api;
        $this->session = $session;
        $this->em = $em;
        $this->add = $add;
        $this->am = $am;
    }

    /**
     * @Route ("/update_product/{productId}")
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function updateProduct(Request $request, $productId): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($productId);
        $name = $request->get('new_name') ?? null;
        $allegroName = $request->get('allegro_name') ?? null;
        $quantity = $request->get('quantity') ?? null;
        if($product && $name && $quantity && $allegroName) {
            $product
                ->setName($name)
                ->setAllegroTitle($allegroName);
            $this->em->flush();

            return new JsonResponse([
                'message' => 'Товар '.$product->getId().' изменен'
            ]);
        } else {
            return new JsonResponse(['message' => 'Товар не найден'], 400);
        }
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

    public function transformJsonBody(Request $request): Request
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
     * @Route ("/get_offer_from_allegro_api/{offerId}")
     * @param $offerId
     * @return JsonResponse
     */
    public function getOfferFromAllegroApi($offerId): JsonResponse
    {
        $response = $this->am->getOfferFromAllegro($offerId);

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/get_products_html")
     * @param Request $request
     * @return Response
     */
    public function getProductsHtml(Request $request): Response
    {
        $word = $request->get('word') ?? null;
        $sort = null;
        if($request->get('sort')) {
            $sort = [
              'column' => $request->get('sort'),
              'method' => $request->get('method')
            ];
        }
        $offset = $limit = $this->session->get('limit') ?? 10;
        $page = $request->get('p') ?? 1;
        if($page == 1 || $page == 0 || $page == null)
        {
            $offset = 0;
            $page = 1;
        }

        $products = $this->em->getRepository(Product::class)->findByArticleAndName($word, $word, $limit, $offset*($page-1), $sort);
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
     * @return JsonResponse
     */
    public function createKit(Request $request): JsonResponse
    {
        $products = [];
        $errors = [];
        $content = json_decode($request->getContent(), true);
        $options = $content['options'];
        $options['deliveryMethod'] = $this->em->getRepository(AllegroDeliveryMethod::class)->find(3);
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

    /**
     * @Route ("/get_unload_products")
     */
    public function getUnloadProducts(): Response
    {
        $products = $this->em->getRepository(Product::class)->getUnloadProducts($this->session->get('currentProfile'));

        return $this->render('api/unloadProducts.html.twig', ['products' => $products]);
    }

    /**
     * @Route ("/get_nonactivate_products")
     */
    public function getNonActivateProducts(): Response
    {
        $exportProducts = [];
//        $criteria = new Criteria();
//        $criteria->where(Criteria::expr()->neq('name', null));
//        $criteria->andWhere(Criteria::expr()->eq('sync', true));
//        $criteria->orderBy(['id' => Criteria::DESC]);
        $products = $this->em->getRepository(Product::class)->getNonActiveProducts();
        foreach ($products as $product) {
            $allegroOffer = $product->getAllegroOffer($this->session->get('currentProfile'));
            if($allegroOffer) {
                if($allegroOffer->getStatus() == false) {
                    $exportProducts[] = $product;
                }
            }
        }

        return $this->render('api/unloadProducts.html.twig', ['products' => $exportProducts]);
    }

    /**
     * @Route ("/upload_to_allegro")
     * @param Request $request
     * @return Response
     */
    public function uploadToAllegro(Request $request): Response
    {
        $allegroOfferRep = $this->em->getRepository(AllegroOffer::class);
        /** @var Product[] $products */
        $products = [];
        $profile = $this->em->getRepository(Profile::class)->find($this->session->get('currentProfile')->getId());
        $content = json_decode($request->getContent(), true);
        if($content) {
            foreach ($content['products'] as $productId) {
                $products[] = $this->em->getRepository(Product::class)->find($productId);
            }
        }
        foreach ($products as $product) {
            $response = $this->am->addOfferToAllegro($product, $profile, $product->getKit());
            if(isset($response['id'])) {
                $allegroOfferRep->createOffer($response['id'], $product, $profile);
                $this->em->flush();
            }
        }
        if(empty($product)) {
            return new Response('Товары не найдены', 400);
        }
        return new Response('ok');
    }

    /**
     * @Route ("/change_product_quantity_everywhere/{productId}")
     * @param $productId
     * @param Request $request
     * @return JsonResponse
     */
    public function changeProductQuantityEverywhere($productId, Request $request): JsonResponse
    {
        $response = [];
        $product = $this->em->getRepository(Product::class)->find($productId);
        $quantity = $request->get('quantity') ?? null;
        //TODO: сделать изменение количество везде
        return new JsonResponse($response);
    }

    /**
     * @Route ("/change_offer_status", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changeOfferStatus(Request $request): JsonResponse
    {
        $command = $request->get('command') ?? null;
        $ids = json_decode($request->getContent(), true);
        $offers = [];
        foreach ($ids as $id) {
            /** @var Product $product */
            $product = $this->em->getRepository(Product::class)->find($id);
            $offers[] = ['id' => $product->getAllegroOffer($this->session->get('currentProfile'))->getAllegroId()];
        }
        if($command) {
                return $this->am->changeStatusOffer($offers, $command, $this->session->get('currentProfile'));
        } else {
            return new JsonResponse(['message' => 'Error'], 400);
        }
    }

    /**
     * @Route ("/get_delivery_methods")
     */
    public function getDeliveryMethods()
    {
        return $this->am->getDeliverySettings();
    }

    /**
     * @Route ("/get_return_policy")
     */
    public function getReturnPolicy()
    {
        return $this->am->getReturnSettings();
    }

    /**
     * @Route ("/get_impl_war")
     */
    public function getImlWar()
    {
        return $this->am->getImplWar();
    }

    /**
     * @Route ("/get_parameters")
     */
    public function getParameters(): JsonResponse
    {
        $profile = $this->em->getRepository(Profile::class)->find(1);
        return $this->am->getParameters('253564', $profile);
    }
}