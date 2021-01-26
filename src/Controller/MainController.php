<?php


namespace App\Controller;


use App\Add\Add;
use App\ebay\Allegro;
use App\ebay\AllegroUserManager;
use App\Entity\Description;
use App\Entity\Order;
use App\Entity\PaymentStatus;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Supply;
use App\Form\CreateOfferType;
use App\Form\DescriptionType;
use App\Form\KitType;
use App\Form\MNumberType;
use App\Form\ProductType;
use App\Form\SaleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\ebay\Ebay;


class MainController extends AbstractController
{
    protected $ebay;
    protected $em;
    protected $allegro;
    protected $session;
    protected $am;

    public function __construct(Ebay $ebay, EntityManagerInterface $em, Allegro $allegro, SessionInterface $session, AllegroUserManager $am)
    {
        $this->ebay = $ebay;
        $this->em = $em;
        $this->allegro = $allegro;
        $this->session = $session;
        $this->am = $am;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        return $this->render('main/main.html.twig', []);
    }

    /**
     * @Route("/products", name="products")
     * @param Request $request
     * @return Response
     */
    public function showProducts(Request $request): Response
    {
        $limit = $this->session->get('limit') ?? 10;
        $page = $request->get('p') == 0 ? 1: $request->get('p');
        $pages = ceil((count($this->em->getRepository(Product::class)->findAll())/$limit));
        $products = $this->em->getRepository(Product::class)->findBy([], null, $limit, ($page-1)*$limit);

        if ($request->get('search'))
        {
            $products = $this->em->getRepository(Product::class)->findByArticleAndName($request->get('search'));
            $pages = 1;
        }

        $forRender = [
            'products' => $products,
            'countOfProducts' => count($products),
            'pages' => $pages,
            'currentPage' => $page ? $page: 1,
            'limit' => $limit
        ];
        return $this->render('products/products.html.twig', $forRender);
    }

    /**
     * @Route("/products/delete/{productId}", name="deleteProduct")
     * @param $productId
     * @return RedirectResponse
     */
    public function deleteProduct($productId): RedirectResponse
    {
        $product = $this->em->getRepository(Product::class)->find($productId);

        if($product)
        {
            $this->em->remove($product);
            $this->em->flush();
        }
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/products/createOfferAllegro/{productId}")
     * @param $productId
     * @return Response
     * @throws \Exception
     */
    public function createOfferAllegro($productId): Response
    {
        $product = $this->em->getRepository(Product::class)->find($productId);
        if($product)
        {
            $form = $this->createForm(CreateOfferType::class);

            $forRender = [
                'form' => $form->createView(),
                'product' => $product
            ];

            return $this->render('products/createAllegroOffer.html.twig', $forRender);
        }
        else
        {
            throw new \Exception('Продукт не найден ((');
        }
    }

    /**
     * @Route("/products/createKit", name="createKit")
     * @param Request $request
     * @param Add $add
     * @return Response
     */
    public function createKit(Request $request, Add $add): Response
    {
        $form = $this->createForm(KitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $data = $form->getData();
            $firstProduct = $data['firstProduct'];
            $options = [
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'article' => $data['article']
            ];
            $add->addKitProduct($firstProduct, $options);

        }

        $forRender = [
            'form' => $form->createView(),
        ];
        return $this->render('products/createkit.html.twig', $forRender);
    }

    /**
     * @Route("/orders", name="orders")
     */
    public function showOrders(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findAllByDate();
        $paymentStatuses = $this->em->getRepository(PaymentStatus::class)->findAll();
        $forRender = [
          'orders'  => $orders,
          'paymentStatuses' => $paymentStatuses,
        ];
        return $this->render('orders/orders.html.twig', $forRender);
    }

    /**
     * @Route("/test", name="test")
     * @param Request $request
     * @return Response
     */
    public function ord(Request $request): Response
    {
        return new Response('');
    }

    /**
     * @Route("/allAuth", name="test2")
     * @param Request $request
     * @return RedirectResponse
     */
    public function test(Request $request): RedirectResponse
    {
        $code = $request->get('code');
        if($code)
        {
            $result = $this->allegro->getTokenForUser($code);
            $this->session->set('userTokenAllegro', $result);
        }
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/item/{itemId}")
     * @param $itemId
     */
    public function item($itemId)
    {

    }

    /**
     * @Route("/supplies/add", name="addProduct")
     * @param Request $request
     * @param Add $add
     * @return Response
     */
    public function addItem(Request $request, Add $add): Response
    {
        $form = $this->createForm(MNumberType::class, null, [
            'add' => $request->get('submit') === '1',
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $quantity = count($data)-2;
            $forRender['quantity'] = $quantity;
            if($request->get('submit') === '2')
            {
                $files = $request->files->all('m_number');
                $result = $add->addDefaultSupply($data, $files);
                return $this->redirectToRoute('supplies');
            }

        }
        $forRender['form'] = $form->createView();

        return $this->render('products/additem.html.twig', $forRender);
    }

    /**
     * @Route("/supplies", name="supplies")
     */
    public function suppliesAction(): Response
    {
        $supplies = $this->em->getRepository(Supply::class)->findAll();
        $forRender['supplies'] = $supplies;
        return $this->render('products/supplies.html.twig', $forRender);
    }

    /**
     * @Route("/products/ebaytome", name="syncFromEbay")
     * @param Add $add
     * @return Response
     */
    public function syncFromEbayAction(Add $add): Response
    {
        $add->syncFromEbay();

        return $this->redirectToRoute('products');

    }

    /**
     * @Route("/products/addToAllegro/{productId}", name="addToAllegro")
     * @param $productId
     * @return JsonResponse
     */
    public function addToAllegro($productId): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($productId);
        return $this->am->changeStatusOffer(9953701920, 'END');
    }//Функция не готова :(


    /**
     * @Route("/supplies/synctoeBay/{supplyId}", name="syncToEbay")
     * @param $supplyId
     * @param Add $add
     * @return RedirectResponse
     */
    public function syncToEbay($supplyId, Add $add): RedirectResponse
    {
        /** @var Supply $supply */
        $supply = $this->em->getRepository(Supply::class)->find($supplyId);
        if($supply)
        {
            if(!$supply->getSync())
            {
                $add->syncToEbay($supply->getProducts());
                $supply->setSync(true);
                $this->em->persist($supply);
                $this->em->flush();
            }
        }
        return $this->redirectToRoute('supplies');
    }

    /**
     * @Route("/products/synctoebay/{productId}", name="syncProductToEbay")
     * @param $productId
     * @param Add $add
     * @return RedirectResponse
     */
    public function syncProductToEbay($productId, Add $add): RedirectResponse
    {
        $product = $this->em->getRepository(Product::class)->findBy(['id' => $productId]);

        if($product)
        {
            if(!$product[0]->getSync())
            {
                $add->syncToEbay($product);
                $product[0]->setSync(true);
                $this->em->persist($product[0]);
                $this->em->flush();
            }
        }
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/orders/syncfromEbay", name="syncOrdersFromEbay")
     * @param Add $add
     * @return RedirectResponse
     */
    public function syncOrdersFromEbay(Add $add)
    {
        $add->syncOrdersFromEbay();
        return $this->redirectToRoute('orders');
    }

    /**
     * @Route("/products/{itemId}", name="cardItem", requirements={"itemId"="\d+"})
     * @param $itemId
     * @param Request $request
     * @return Response
     */
    public function cardItem($itemId, Request $request): Response
    {
        $forRender = [];
        $product = $this->em->getRepository(Product::class)->find($itemId);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $product->setName($form->getData()->getName());
            $this->em->flush();
            return $this->redirectToRoute('products');
        }
        $forRender = [
            'product' => $product,
            'form' => $form->createView(),
        ];
        return $this->render('products/card.html.twig', $forRender);
    }

    /**
     *
     * @Route("/sales", name="sales")
     *
     */
    public function sales(): Response
    {
        $sales = $this->em->getRepository(Sale::class)->findAll();
        $forRender['sales'] = $sales;

        return $this->render('sales/sales.html.twig', $forRender);
    }

    /**
     *
     * @Route("/sales/addsale", name="addSale")
     * @param Request $request
     * @param Add $add
     * @return Response
     */
    public function addSale(Request $request, Add $add): Response
    {
        $form = $this->createForm(SaleType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            if($data['createAt'] && $data['orderNumber'] && $data['product'])
            {
                $add->addSale($data);
                return $this->redirectToRoute('sales');
            }
        }

        $forRender['form'] = $form->createView();

        return $this->render('sales/addsale.html.twig', $forRender);

    }

    /**
     * @Route("/book", name="handbook")
     */
    public function handbookAction(): Response
    {
        $descriptions = $this->em->getRepository(Description::class)->findAll();
        $forRender = [
            'descriptions' => $descriptions,
        ];
        return $this->render('handbook/handbook.html.twig', $forRender);
    }

    /**
     * @Route("/book/{groupId}", name="editGroup")
     * @param $groupId
     * @param Request $request
     * @return Response
     */
    public function editGroupAction($groupId, Request $request): Response
    {
        $description = $this->em->getRepository(Description::class)->find($groupId);
        if($description)
        {
            $form = $this->createForm(DescriptionType::class, $description);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $this->em->flush();

                return $this->redirectToRoute('handbook');
            }

            $forRender = [
              'form' => $form->createView()
            ];
            return $this->render('handbook/editdes.html.twig', $forRender);
        }
        else
        {
            throw new Exception('Товарная группа не найдена');
        }
    }

    /**
     * @Route("/sales/salecard/id{saleId}", name="salecard")
     * @param $saleId
     * @param Request $request
     * @return Response
     */
    public function saleCardAction($saleId, Request $request): Response
    {
        $sale = $this->em->getRepository(Sale::class)->find($saleId);

        if($sale)
        {
            $form = $this->createForm(SaleType::class, $sale);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $this->em->persist($sale);
                $this->em->flush();

                return $this->redirectToRoute('sales');
            }
        }
        else
        {
            throw new Exception('Продажа не найдена');
        }
        $forRender = [
            'form' => $form->createView(),
        ];
        return $this->render('sales/salecard.html.twig', $forRender);
    }

    /**
     *@Route("/products/sync_from_alegro", name="syncFromAllegro")
     */
    public function syncFromAllegro(): RedirectResponse
    {
        $this->am->getOffersFromAllegro();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/orders/sync_from_allegro", name="syncOrdersFromAllegro")
     */
    public function syncOrdersFromAllegro()
    {
        try {
            $this->am->syncOrdersFromAllegro();
        }catch(\Exception $e){
            $this->addFlash('notice','Ошибка подключения к Allegro');
        }

        return $this->redirectToRoute('orders');
    }

    /**
     * @Route("/orders/{orderId}", name="orderCard")
     * @param $orderId
     * @return Response
     */
    public function orderCard($orderId): Response
    {
        $response = $this->am->getAnOrder($orderId);

        return $this->render('orders/card.html.twig', [
            'response' => json_decode($response, true)
        ]);
    }
}