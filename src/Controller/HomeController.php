<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(MenuRepository $menuRepo, AvisRepository $avisRepo): Response
    {
        // Récupérer les menus actifs avec le nombre de plats
        $menus = $menuRepo->findActiveMenus();
        
        // Récupérer les avis publiés (validés)
        $avis = $avisRepo->findPublishedReviews();
        
        return $this->render('home/index.html.twig', [
            'menus' => $menus,
            'avis' => $avis,
        ]);
    }

    #[Route('/apropos', name: 'app_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}