<?php


namespace App\Controller\api\AuthorizationApi;

use App\ebay\AllegroUserManager;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthAllegroApi
 * @package App\Controller\api\AuthorizationApi
 * @Route ("/allegro_auth")
 */
class AuthAllegroApi extends AbstractController
{
    const API_TOKEN = 'SECRET_WORD_FOR_MY_API322';
    private $am;
    private $em;
    private $profileRep;

    public function __construct(AllegroUserManager $am, EntityManagerInterface $em)
    {
        $this->am = $am;
        $this->em = $em;
        $this->profileRep = $this->em->getRepository(Profile::class);
    }

    /**
     * @Route ("/update_tokens", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function setTokens(Request $request): JsonResponse
    {
        if($request->get('API_TOKEN') === self::API_TOKEN) {
            /** @var Profile[] $profiles */
            $profiles = $this->profileRep->findAllAllegro();
            foreach ($profiles as $profile) {
                try {
                    $tokens = $this->am->getTokenForUserWithRefreshToken($profile);
                    $profile->setAllegroAccessToken($tokens['accessToken']);
                    $profile->setAllegroRefreshToken($tokens['refreshToken']);
                    $this->em->flush();
                } catch (\Exception $e) {}
            }
            return new JsonResponse(['message' => 'ok']);
        }

        return new JsonResponse(['message' => 'Ошибка авторизации']);
    }
}