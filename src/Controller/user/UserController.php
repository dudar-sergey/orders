<?php


namespace App\Controller\user;

use App\Form\MyUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller\user
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return Response
     */
    public function userAction(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(MyUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $user = $form->getData();
            $this->em->flush();
        }

        $forRender = [
            'form' => $form->createView()
        ];
        return $this->render('User/profile.html.twig', $forRender);
    }
}