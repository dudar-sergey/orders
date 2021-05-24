<?php


namespace App\Controller\api\ProductApi;


use App\Add\ImageManager;
use App\Add\ProductManager;
use App\ebay\AllegroUserManager;
use App\Entity\AllegroOffer;
use App\Entity\Description;
use App\Entity\Product;
use App\Entity\Profile;
use App\Entity\Supply;
use App\Entity\SupplyProduct;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Class ProductApi
 * @package App\Controller\api\ProductApi
 * @Route ("/product_api")
 */
class ProductApi extends AbstractController
{
    private $productRep;
    private $em;
    private $productManager;
    private $imageManager;

    public function __construct(EntityManagerInterface $em, ProductManager $productManager, ImageManager $imageManager)
    {
        $this->productRep = $em->getRepository(Product::class);
        $this->em = $em;
        $this->productManager = $productManager;
        $this->imageManager = $imageManager;
    }

    /**
     * @Route ("/get_table_product_art_name")
     * @param Request $request
     * @return Response
     */
    public function getTableOfProductsWithArticleAndName(Request $request): Response
    {
        $word = $request->get('word') ?? null;
        $products = $this->productRep->findByArticleAndName($word, $word);

        $forRender = [
            'products' => $products
        ];
        return $this->render('api/productTableArtAndName.html.twig', $forRender);
    }

    /**
     * @Route("/get_allegro_offer/{allegroOffer}")
     * @param $allegroOffer
     * @param AllegroUserManager $am
     */
    public function getAllegroOffer($allegroOffer, AllegroUserManager $am)
    {
        $allegroOffer = $this->em->getRepository(AllegroOffer::class)->findOneBy(['allegroId' => $allegroOffer]);
        return new JsonResponse($am->getOfferFromAllegro($allegroOffer->getAllegroId(), $allegroOffer->getProfile()));
    }

    /**
     * @Route ("/upload_products", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadProducts(Request $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $data = $request->request->all();
        if($file) {
            /** @var Supply $supply */
            $supplyProducts = $this->productManager->addSupply($file, $data['sender'], $data['recipient'], new DateTime($data['date']), $data['contract']);
            return new JsonResponse(['message' => 'Добавлена поставка, количество позиций '.count($supplyProducts)]);
        } else {
            return new JsonResponse(['message' => 'Нет файла'], 400);
        }
    }

    /**
     * @Route ("/update_quantity", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        $response = [];
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if($file) {
            $response = $this->productManager->updateQuantity($file);
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/change_group/{groupId}")
     * @param int $groupId
     * @param Request $request
     * @return JsonResponse
     */
    public function changeGroup(int $groupId, Request $request): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($request->get('product_id'));
        $group = $this->em->getRepository(Description::class)->find($groupId);
        if($group) {
            $product->setDes($group);
        }
        $this->em->flush();
        return new JsonResponse(['message' => 'ok']);
    }

    /**
     * @Route("/images/create", methods={"POST"})
     */
    public function createImages(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'];
        $profileId = $data['profile'];
        var_dump($data);
        if($profileId && $url) {
            $this->imageManager->createNewImagesForProfile($url, $this->em->getRepository(Profile::class)->find($profileId));
        }
        return new JsonResponse(['message' => 'ok']);
    }
}