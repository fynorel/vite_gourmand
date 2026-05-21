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
        $theme        = $request->query->get('theme')        ?: null;
        $regime       = $request->query->get('regime')       ?: null;
        $prixMax      = $request->query->get('prixMax')      ? (float)$request->query->get('prixMax')      : null;
        $prixMin      = $request->query->get('prixMin')      ? (float)$request->query->get('prixMin')      : null;
        $prixMaxRange = $request->query->get('prixMaxRange') ? (float)$request->query->get('prixMaxRange') : null;
        $nbPersonnes  = $request->query->get('nbPersonnes')  ? (int)$request->query->get('nbPersonnes')    : null;

        $menus = $menuRepo->findActiveMenusWithFilters(
            $theme,
            $regime,
            $prixMax,
            $prixMin,
            $prixMaxRange,
            $nbPersonnes
        );

        return $this->render('menu/list.html.twig', [
            'menus'          => $menus,
            'theme_filter'   => $theme,
            'regime_filter'  => $regime,
            'prixMax_filter' => $prixMax,
            'prixMin_filter' => $prixMin,
            'prixMaxRange_filter' => $prixMaxRange,
            'nbPersonnes_filter'  => $nbPersonnes,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_detail', methods: ['GET'])]
    public function detail(int $id, MenuRepository $menuRepo): Response
    {
        $menu = $menuRepo->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Menu non trouvé');
        }

        $rating = $menuRepo->findMenuRating($id);

        return $this->render('menu/detail.html.twig', [
            'menu'   => $menu,
            'rating' => $rating,
        ]);
    }
}