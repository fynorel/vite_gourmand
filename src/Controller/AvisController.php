<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/avis')]
class AvisController extends AbstractController
{
    #[Route('', name: 'app_avis_list', methods: ['GET'])]
    public function list(AvisRepository $avisRepo): Response
    {
        // Récupérer les avis publiés
        $avisPublies = $avisRepo->findBy(['statut' => 'VALIDE'], ['dateCreation' => 'DESC']);
        
        return $this->render('avis/list.html.twig', [
            'avis' => $avisPublies,
        ]);
    }

    #[Route('/commande/{id}/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(int $id, Request $request, CommandeRepository $commandeRepo, EntityManagerInterface $em): Response
    {
        $commande = $commandeRepo->find($id);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }
        
        // Vérifier que la commande appartient à l'utilisateur
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
        
        // Vérifier que la commande n'a pas déjà un avis
        if ($commande->getAvis()) {
            $this->addFlash('info', 'Vous avez déjà donné un avis pour cette commande');
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }
        
        // Vérifier que la commande est livrée
        if ($commande->getStatut() !== 'LIVRE') {
            $this->addFlash('error', 'Vous pouvez only donner un avis pour une commande livrée');
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }
        
        if ($request->isMethod('POST')) {
            $note = (int) $request->request->get('note');
            $commentaire = $request->request->get('commentaire');
            
            if ($note < 1 || $note > 5) {
                $this->addFlash('error', 'La note doit être entre 1 et 5');
                return $this->redirectToRoute('app_avis_new', ['id' => $commande->getId()]);
            }
            
            if (empty($commentaire)) {
                $this->addFlash('error', 'Le commentaire est obligatoire');
                return $this->redirectToRoute('app_avis_new', ['id' => $commande->getId()]);
            }
            
            // Créer l'avis
            $avis = new Avis();
            $avis->setCommande($commande);
            $avis->setUtilisateur($this->getUser());
            $avis->setNote($note);
            $avis->setCommentaire($commentaire);
            $avis->setStatut('EN_ATTENTE'); // En attente de modération
            
            $em->persist($avis);
            $em->flush();
            
            $this->addFlash('success', 'Avis soumis pour modération');
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }
        
        return $this->render('avis/new.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_detail', methods: ['GET'])]
    public function detail(Avis $avis): Response
    {
        // Vérifier que l'avis est publié
        if ($avis->getStatut() !== 'VALIDE') {
            if (!$this->isGranted('ROLE_ADMIN') && $avis->getUtilisateur() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Cet avis n\'est pas accessible');
            }
        }
        
        return $this->render('avis/detail.html.twig', [
            'avis' => $avis,
        ]);
    }
}