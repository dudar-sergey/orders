<?php


namespace App\Controller\Update;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UpdateController extends AbstractController
{
    /**
     * @Route("/update", name="update")
     */
    public function updateIndex(): Response
    {
        $forRender = [

        ];
        return $this->render('update/update.html.twig', $forRender);
    }
}