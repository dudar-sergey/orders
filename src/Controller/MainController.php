<?php


namespace App\Controller;


use App\Add\Add;
use App\ebay\Allegro;
use App\ebay\AllegroUserManager;
use App\Entity\Description;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductGroup;
use App\Entity\Sale;
use App\Entity\Supply;
use App\Form\CreateOfferType;
use App\Form\DescriptionType;
use App\Form\KitType;
use App\Form\ManyDescriptionType;
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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MainController extends AbstractController
{
    private $ebay;
    private $em;
    private $allegro;
    private $session;
    private $am;

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
    public function index()
    {

        return $this->render('main/main.html.twig', []);
    }

    /**
     * @Route("/products", name="products")
     * @return Response
     */
    public function showProducts()
    {
        $products = $this->em->getRepository(Product::class)->findAll();
        $forRender = [
            'products' => $products,
            'countOfProducts' => count($products),

        ];
        return $this->render('products/products.html.twig', $forRender);
    }

    /**
     * @Route("/products/delete/{productId}", name="deleteProduct")
     * @param $productId
     * @return RedirectResponse
     */
    public function deleteProduct($productId)
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
    public function createOfferAllegro($productId)
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
    public function createKit(Request $request, Add $add)
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
    public function showOrders()
    {
        $orders = $this->em->getRepository(Order::class)->findAll();
        $forRender['orders'] = $orders;
        return $this->render('orders/orders.html.twig', $forRender);
    }

    /**
     * @Route("/test", name="test")
     */
    public function ord()
    {
        $product = $this->em->getRepository(Product::class)->find(61);
        //$response = $this->am->getOfferFromAllegro(9939962050, true);
        //$response = $this->am->addOfferToAllegro($product);
        $response = $this->am->changeStatusOffer(9939962050, 'END');

        return new JsonResponse($response->getContent(), 200, [], true);
    }

    /**
     * @Route("/allAuth", name="test2")
     * @param Request $request
     * @return RedirectResponse
     */
    public function test(Request $request)
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
    public function addItem(Request $request, Add $add)
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
    public function suppliesAction()
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
    public function syncFromEbayAction(Add $add)
    {
        $add->syncFromEbay();

        return $this->redirectToRoute('products');

    }

    /**
     * @Route("/products/addToAllegro/{productId}", name="addToAllegro")
     * @param $productId
     * @return JsonResponse
     */
    public function addToAllegro($productId)
    {
        $product = $this->em->getRepository(Product::class)->find($productId);
        return $this->am->changeStatusOffer(9953701920, 'END');
    }


    /**
     * @Route("/supplies/synctoeBay/{supplyId}", name="syncToEbay")
     * @param $supplyId
     * @param Add $add
     * @return RedirectResponse
     */
    public function syncToEbay($supplyId, Add $add)
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
    public function syncProductToEbay($productId, Add $add)
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
     * @Route("/products/{itemId}", name="cardItem")
     * @param $itemId
     * @param Request $request
     * @return Response
     */
    public function cardItem($itemId, Request $request)
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
    public function sales()
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
    public function addSale(Request $request, Add $add)
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
    public function handbookAction()
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
    public function editGroupAction($groupId, Request $request)
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
    public function saleCardAction($saleId, Request $request)
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
}