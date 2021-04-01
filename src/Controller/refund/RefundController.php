<?php


namespace App\Controller\refund;


use App\Entity\Refund;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefundController extends AbstractController
{
    private $refundRep;
    private $em;



    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->refundRep = $this->em->getRepository(Refund::class);
    }

    /**
     * @Route ("/refund", name="refund")
     */
    public function refund(): Response
    {
        $refunds = $this->refundRep->findAll();

        $forRender = [
            'refunds' => $refunds
        ];
        return $this->render('refund/refund.html.twig', $forRender);
    }
}