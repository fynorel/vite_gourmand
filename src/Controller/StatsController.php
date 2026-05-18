<?php

namespace App\Controller;

use App\Service\CommandeStatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class StatsController extends AbstractController
{
    #[Route('/admin/stats', name: 'app_stats_dashboard')]
    public function dashboard(CommandeStatService $statService): Response
    {
        $statsChart = $statService->getStatsForChart();
        $statsByDate = $statService->getStatsByDate();

        // Préparer les données pour Chart.js
        $labels = array_map(fn($stat) => $stat['_id']['menuTitre'] ?? 'N/A', $statsChart);
        $data = array_map(fn($stat) => $stat['total'] ?? 0, $statsChart);

        return $this->render('stats/dashboard.html.twig', [
            'labels' => json_encode($labels),
            'data' => json_encode($data),
            'statsChart' => $statsChart,
            'statsByDate' => $statsByDate,
        ]);
    }
}
