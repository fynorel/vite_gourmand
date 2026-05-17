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
    public function index(CommandeRepository $commandeRepo, MenuRepository $menuRepo): Response
    {
        // Récupérer les données du dashboard
        $dashboardData = $commandeRepo->getDashboardData();
        
        // Récupérer les commandes actives
        $commandesActives = $commandeRepo->findActiveOrders();
        
        // Récupérer les menus avec le nombre de plats
        $menusActifs = $menuRepo->findActiveMenus();
        
        return $this->render('dashboard/index.html.twig', [
            'dashboard' => $dashboardData,
            'commandes_actives' => $commandesActives,
            'menus_actifs' => $menusActifs,
        ]);
    }

    #[Route('/commandes', name: 'app_dashboard_commandes', methods: ['GET'])]
    public function commandes(CommandeRepository $commandeRepo): Response
    {
        $commandesActives = $commandeRepo->findActiveOrders();
        
        return $this->render('dashboard/commandes.html.twig', [
            'commandes' => $commandesActives,
        ]);
    }

    #[Route('/menus', name: 'app_dashboard_menus', methods: ['GET'])]
    public function menus(MenuRepository $menuRepo): Response
    {
        $menusActifs = $menuRepo->findActiveMenus();
        $ratings = $menuRepo->findMenuRatings();
        
        // Fusionner les ratings avec les menus
        $menusWithRatings = array_map(function ($menu) use ($ratings) {
            $rating = array_filter($ratings, fn($r) => $r['id_menu'] == $menu['id_menu']);
            $ratingData = array_shift($rating) ?: [];
            return array_merge($menu, $ratingData);
        }, $menusActifs);
        
        return $this->render('dashboard/menus.html.twig', [
            'menus' => $menusWithRatings,
        ]);
    }

    #[Route('/avis', name: 'app_dashboard_avis', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function avis(AvisRepository $avisRepo): Response
    {
        // Récupérer les avis en attente de modération
        $avisEnAttente = $avisRepo->findBy(['statut' => 'EN_ATTENTE'], ['dateCreation' => 'DESC']);
        
        return $this->render('dashboard/avis.html.twig', [
            'avis' => $avisEnAttente,
        ]);
    }

    #[Route('/avis/{id}/valider', name: 'app_dashboard_avis_valider', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function validerAvis(int $id, AvisRepository $avisRepo): Response
    {
        // À implémenter
        return $this->redirectToRoute('app_dashboard_avis');
    }
}