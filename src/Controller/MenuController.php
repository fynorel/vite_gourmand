<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menus')]
class MenuController extends AbstractController
{
    #[Route('', name: 'app_menu_list', methods: ['GET'])]
    public function list(MenuRepository $menuRepo, Request $request): Response
    {
        // Récupérer les filtres depuis la requête
        $theme = $request->query->get('theme');
        $regime = $request->query->get('regime');
        
        // Récupérer les menus actifs avec filtres
        $menus = $menuRepo->findActiveMenus();
        
        // Appliquer les filtres côté PHP si nécessaire
        if ($theme) {
            $menus = array_filter($menus, fn($m) => $m['theme'] === $theme);
        }
        if ($regime) {
            $menus = array_filter($menus, fn($m) => $m['regime'] === $regime);
        }
        
        return $this->render('menu/list.html.twig', [
            'menus' => $menus,
            'theme_filter' => $theme,
            'regime_filter' => $regime,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_detail', methods: ['GET'])]
    public function detail(int $id, MenuRepository $menuRepo): Response
    {
        $menu = $menuRepo->find($id);
        
        if (!$menu) {
            throw $this->createNotFoundException('Menu non trouvé');
        }
        
        // Récupérer les notes moyennes pour ce menu
        $ratings = $menuRepo->findMenuRating($id);
        
        return $this->render('menu/detail.html.twig', [
            'menu' => $menu,
            'rating' => $ratings,
        ]);
    }
}