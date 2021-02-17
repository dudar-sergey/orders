<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnloadController extends MainController
{
    /**
     * @Route("/unload", name="unload")
     */
    public function unloads(): Response
    {

        return $this->render('unload/unloads.html.twig', []);
    }
}