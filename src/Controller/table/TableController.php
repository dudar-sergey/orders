<?php


namespace App\Controller\table;

use App\Controller\MainController;
use App\ebay\AllegroManager;
use App\Entity\Product;
use App\Entity\Profile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TableController extends MainController
{
    /**
     * @Route("/table", name="table")
     * @param AllegroManager $allegroManager
     * @return Response
     */
    public function tableIndex(AllegroManager $allegroManager): Response
    {
        $array = [];
        /** @var Product[] $products */
        $products = $this->em->getRepository(Product::class)->findBy([], [], 3);
        $profiles = $this->em->getRepository(Profile::class)->findAll();
        foreach ($products as $product) {
            foreach ($profiles as $profile) {
                if($product->getAllegroOffer($profile))
                {
                    $array[$product->getArticul()][] = json_decode($allegroManager->getOfferById($product->getAllegroOffer($profile)->getAllegroId(), $profile), true);
                }
            }
        }
        $forRender = [
            'profiles' => $profiles,
            'products' => $array
        ];
        return $this->render('table/table.html.twig', $forRender);
    }
}