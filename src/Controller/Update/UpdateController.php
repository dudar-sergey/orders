<?php


namespace App\Controller\Update;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UpdateController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/update", name="update")
     */
    public function updateIndex(): Response
    {
        $forRender = [
            'profiles' => $this->em->getRepository(Profile::class)->findAll(),
        ];
        return $this->render('update/update.html.twig', $forRender);
    }
}