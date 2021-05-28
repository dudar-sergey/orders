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
        $products = $this->em->getRepository(Product::class)->findBy([], ['quantity' => 'DESC']);
        $profiles = $this->em->getRepository(Profile::class)->findAll();
        $forRender = [
            'profiles' => $profiles,
            'products' => $products,
        ];
        return $this->render('table/table.html.twig', $forRender);
    }
}