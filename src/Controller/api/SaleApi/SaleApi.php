<?php


namespace App\Controller\api\SaleApi;


use App\Add\Add;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Sale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;


/**
 * Class SaleApi
 * @package App\Controller\api\SaleApi
 * @Route ("/api")
 */
class SaleApi extends AbstractController
{
    private $em;
    private $add;

    public function __construct(EntityManagerInterface $em, Add $add)
    {
        $this->em = $em;
        $this->add = $add;
    }

    /**
     * @Route ("/create_sale", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createSale(Request $request): JsonResponse
    {
        $response = json_decode($request->getContent(), true);
        $product = $this->em->getRepository(Product::class)->find($response['product']);
        $order = $this->em->getRepository(Order::class)->createUpdateOrder([
           'price' => $response['price'],
           'buyer' => $response['buyer'],
           'date' => new \DateTime('now'),
           'placement' => $response['platform'],
           'product' => $product
        ]);
//        $this->em->getRepository(Sale::class)->createSale([
//            'order' => $order
//        ]);
        $this->em->flush();
        //$this->add->changeQuantityProduct();
        return new JsonResponse(['message' => 'Продажа добавлена']);
    }

    /**
     * @Route("/get_sale_html/{saleId}")
     * @param $saleId
     * @return Response
     */
    public function getSaleHtml($saleId): Response
    {
        $sale = $this->em->getRepository(Sale::class)->find($saleId);

        return $this->render('api/sale.html.twig', ['sale' => $sale]);
    }
}