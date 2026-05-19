<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_EMPLOYE')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(
        CommandeRepository $commandeRepo,
        MenuRepository $menuRepo,
        AvisRepository $avisRepo
    ): Response
    {
        $dashboardData = $commandeRepo->getDashboardData();
        $menus = $menuRepo->findActiveMenus();
        $avis = $avisRepo->findPublishedReviews();

        return $this->render('dashboard/index.html.twig', [
            'dashboard' => $dashboardData,
            'menus' => $menus,
            'avis' => $avis,
        ]);
    }

    #[Route('/commandes', name: 'app_dashboard_commandes', methods: ['GET'])]
    public function commandes(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findActiveOrders();

        return $this->render('dashboard/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/menus', name: 'app_dashboard_menus', methods: ['GET'])]
    public function menus(MenuRepository $menuRepo): Response
    {
        $menus = $menuRepo->findActiveMenus();
        $ratings = $menuRepo->findMenuRatings();

        return $this->render('dashboard/menus.html.twig', [
            'menus' => $menus,
            'ratings' => $ratings,
        ]);
    }
    #[Route('/avis', name: 'app_dashboard_avis', methods: ['GET'])]
    public function avis(AvisRepository $avisRepo): Response
    {
        $avis = $avisRepo->findPublishedReviews();

        return $this->render('dashboard/avis.html.twig', [
            'avis' => $avis,
        ]);
    }




}
