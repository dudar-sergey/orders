<?php


namespace App\Controller\api\ProfileApi;

use App\Add\Add;
use App\Add\ProductPlacementManager;
use App\ebay\AllegroUserManager;
use App\Entity\AllegroOffer;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileApi
 * @package App\Controller\api\ProfileApi
 * @Route ("/api")
 */
class ProfileApi extends AbstractController
{
    private $session;
    private $profileRep;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->session = $session;
        $this->profileRep = $this->em->getRepository(Profile::class);
    }

    /**
     * @Route ("/set_profile")
     * @param Request $request
     * @return JsonResponse
     */
    public function setCurrentProfile(Request $request): JsonResponse
    {
        $profile = $this->profileRep->find($request->get('profile_id'));
        if($profile) {
            if($this->getUser()->hasProfile($profile)) {
                $this->session->set('currentProfile', $profile);
                return new JsonResponse(['profile' => [
                    'name' => $profile->getName(),
                    'clientId' => $profile->getClientId(),
                    'clientSecret' => $profile->getClientSecret(),
                ]]);
            }
        }
        return new JsonResponse(['message' => 'Ошибка авторизации']);
    }

    /**
     * @Route ("/sync_quantity_allegro/{profileId}")
     * @param ProductPlacementManager $ppm
     * @param $profileId
     * @param Request $request
     * @return JsonResponse
     */
    public function syncQuantityAllegro(ProductPlacementManager $ppm, $profileId, Request $request): JsonResponse
    {
        $this->session->save();
        $processId = $request->get('process_id');
        $profile = $this->em->getRepository(Profile::class)->find($profileId);
        $ppm->syncQuantityAllegro($profile, $processId);
        return new JsonResponse(['message' => 'ok']);
    }
}