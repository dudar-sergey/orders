<?php


namespace App\Controller\user;

use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return Response
     */
    public function userAction(Request $request): Response
    {
        $user = $this->getUser();
        $profiles = $user->getProfiles();
        /** @var Profile $currentProfile */
        $currentProfile = $this->session->get('currentProfile') ?? null;
        if($currentProfile) {
            $forRender['allegroAuthUrl'] = 'https://allegro.pl/auth/oauth/authorize?response_type=code&client_id='.$currentProfile->getClientId().'&redirect_uri=https://localhost:8000/allAuth&promt=none';
        }
        $forRender = array_merge($forRender ?? [], [
            'profiles' => $profiles,
            'currentProfile' => $currentProfile,
        ]);
        return $this->render('User/profile.html.twig', $forRender);
    }
}