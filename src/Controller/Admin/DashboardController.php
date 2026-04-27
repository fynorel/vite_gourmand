<?php

namespace App\Controller\Admin;

use App\Service\MenuStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private MenuStatsService $statsService,
    ) {}

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        // Récupérer les données pour le graphique principal
        $chartData = $this->statsService->getDataForChart();

        // Récupérer les top 5 menus les plus commandés
        $topMenusByOrders = $this->statsService->getTopMenus(5);

        // Récupérer les top 5 menus les plus rentables
        $topMenusByRevenue = $this->statsService->getTopMenusByRevenue(5);

        // Calculer les totaux
        $totalOrders = array_sum(array_map(fn($stat) => $stat['totalOrders'], $chartData));
        $totalRevenue = array_sum(array_map(fn($stat) => $stat['revenue'], $chartData));

        return $this->render('admin/dashboard.html.twig', [
            'chartData' => $chartData,
            'topMenusByOrders' => $topMenusByOrders,
            'topMenusByRevenue' => $topMenusByRevenue,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    #[Route('/dashboard/chart-data', name: 'chart_data')]
    public function getChartData(): Response
    {
        $data = $this->statsService->getDataForChart();

        return $this->json([
            'labels' => array_map(fn($stat) => $stat['title'], $data),
            'orders' => array_map(fn($stat) => $stat['orders'], $data),
            'revenue' => array_map(fn($stat) => $stat['revenue'], $data),
        ]);
    }

    #[Route('/dashboard/sync', name: 'sync_stats')]
    public function syncStats(): Response
    {
        // Cette route peut être appelée pour forcer la synchronisation
        // En production, utilisez plutôt : php bin/console app:sync-menu-stats

        return $this->json([
            'message' => 'Utilisez la commande : php bin/console app:sync-menu-stats',
        ], 202);
    }
}
