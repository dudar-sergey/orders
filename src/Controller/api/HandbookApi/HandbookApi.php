<?php


namespace App\Controller\api\HandbookApi;

use App\Entity\Description;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/api")
 */
class HandbookApi extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route ("/create_group", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if(isset($data['ruName'])) {
            $group = $this->em->getRepository(Description::class)->createGroup($data['ruName']);
            $group = [
                'id' => $group->getId(),
                'ruName' => $group->getRuName()
            ];
        }
        return new JsonResponse(['group' => $group ?? null]);
    }
}
