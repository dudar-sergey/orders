<?php


namespace App\Controller\api\OrderApi;


use App\Add\Add;
use App\Add\OrdersManager;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ListenerOrderApi
 * @package App\Controller\api\OrderApi
 * @Route ("/order_api")
 */
class ListenerOrderApi extends AbstractController
{
    const API_TOKEN = 'SECRET_WORD_FOR_MY_API322';
    private $em;
    private $profileRep;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->profileRep = $this->em->getRepository(Profile::class);
    }

    /**
     * @Route ("/update_order", methods={"GET"})
     * @param Request $request
     * @param OrdersManager $om
     * @return JsonResponse
     */
    public function updateOrder(Request $request, OrdersManager $om): JsonResponse
    {
        $newOrders = [];
        if($request->get('API_TOKEN') === self::API_TOKEN) {
            $allegroProfiles = $this->profileRep->findAllAllegro();
            foreach ($allegroProfiles as $profile) {
                $newOrders = $om->syncOrdersFromAllegro($profile);
            }
            return new JsonResponse(['message' => 'Новых закзаов '.count($newOrders)]);
        }

        return new JsonResponse(['message' => 'Ошибка авторизации']);
    }
}