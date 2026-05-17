<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    #[Route('', name: 'app_commande_list', methods: ['GET'])]
    public function list(CommandeRepository $commandeRepo): Response
    {
        // Récupérer les commandes de l'utilisateur actuellement connecté
        $commandes = $commandeRepo->findBy([
            'utilisateur' => $this->getUser()
        ], ['dateCreation' => 'DESC']);
        
        return $this->render('commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_detail', methods: ['GET'])]
    public function detail(Commande $commande): Response
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($commande->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
        
        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuRepository $menuRepo, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $menuId = $request->request->get('menu_id');
            $nbPersonnes = (int) $request->request->get('nb_personnes');
            $adresse = $request->request->get('adresse');
            $datePrestation = new \DateTimeImmutable($request->request->get('date_prestation'));
            
            $menu = $menuRepo->find($menuId);
            if (!$menu) {
                $this->addFlash('error', 'Menu non trouvé');
                return $this->redirectToRoute('app_menu_list');
            }
            
            // Créer la commande
            $commande = new Commande();
            $commande->setUtilisateur($this->getUser());
            $commande->setMenu($menu);
            $commande->setNbPersonnes($nbPersonnes);
            $commande->setAdresse($adresse);
            $commande->setDatePrestation($datePrestation);
            $commande->setPrixMenu($menu->getPrix());
            $commande->setPrixTotal($menu->getPrix());
            $commande->setStatut('EN_ATTENTE');
            
            $em->persist($commande);
            $em->flush();
            
            $this->addFlash('success', 'Commande créée avec succès');
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }
        
        $menus = $menuRepo->findBy(['actif' => true]);
        
        return $this->render('commande/new.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/{id}/annuler', name: 'app_commande_cancel', methods: ['POST'])]
    public function cancel(Commande $commande, EntityManagerInterface $em, Request $request): Response
    {
        // Vérifier que la commande appartient à l'utilisateur ou que c'est un admin
        if ($commande->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
        
        // Vérifier que le token CSRF est valide
        if (!$this->isCsrfTokenValid('cancel_' . $commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }
        
        if ($commande->getStatut() === 'EN_ATTENTE') {
            $commande->setStatut('ANNULEE');
            $em->flush();
            $this->addFlash('success', 'Commande annulée');
        } else {
            $this->addFlash('error', 'Impossible d\'annuler cette commande');
        }
        
        return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
    }
}