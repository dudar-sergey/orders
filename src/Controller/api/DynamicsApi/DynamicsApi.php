<?php


namespace App\Controller\api\DynamicsApi;


use App\Add\Statistic;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DynamicsApi
 * @package App\Controller\api\DynamicsApi
 * @Route ("/api")
 */
class DynamicsApi extends AbstractController
{
    private $statistic;


    public function __construct(Statistic $statistic)
    {
        $this->statistic = $statistic;
    }

    /**
     * @Route ("/get_dynamics_html")
     * @param Request $request
     */
    public function getDynamics(Request $request)
    {
        $positions = [];
        $startDate = $request->get('startDate') ?? null;
        $endDate = $request->get('endDate') ?? null;
        if($startDate) {
            $positions = $this->statistic->getSaleSpeedByDate(new DateTime($startDate), new DateTime($endDate));
        }

        return $this->render('api/dynamicsTable.html.twig', ['positions' => $positions]);
    }
}