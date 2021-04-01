<?php


namespace App\Controller\api\ProgressApi;

use App\Entity\Progress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProgressApi
 * @package App\Controller\api\ProgressApi
 * @Route ("/api/progress")
 */
class ProgressApi extends AbstractController
{
    private $em;
    private $progressRep;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->progressRep = $this->em->getRepository(Progress::class);
        $this->session = $session;
    }

    /**
     * @Route ("/check_progress", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkProgress(Request $request): JsonResponse
    {
        $processId = $request->get('process_id') ?? null;
        $progress = $this->progressRep->findOneBy(['processId' => $processId]);
        if ($progress) {
            $response = [
                'processId' => $processId,
                'percent' => $progress->getPercent(),
                'message' => $progress->getMessage(),
            ];
            return new JsonResponse($response);
        } else {
            return new JsonResponse(['error' => 'Не найден id процесса'], 400);
        }
    }

    /**
     * @Route ("/init_progress")
     * @param Request $request
     * @return JsonResponse
     */
    public function initProgress(Request $request): JsonResponse
    {
        $processId = $request->get('process_id');
        $this->progressRep->createProgress($processId, 0);
        return new JsonResponse(['message' => 'ok']);
    }

    /**
     * @Route ("/test")
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        $this->session->save();
        $processId = $request->get('processId');
        $n = 100;
        for ($i = 0; $i < $n; $i++) {
            sleep(1);
            $this->progressRep->updateProgress($processId, $i / $n * 100);
        }
        $this->progressRep->updateProgress($processId, 100);
        return new JsonResponse(['message' => 'ok']);
    }
}