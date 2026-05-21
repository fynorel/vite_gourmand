<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    // ── Mon profil ──────────────────────────────────────────────────────────
    #[Route('', name: 'app_profile', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepo): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        $commandes = $commandeRepo->findByUtilisateur($user->getId());

        return $this->render('profil/index.html.twig', [
            'user'      => $user,
            'commandes' => $commandes,
        ]);
    }

    // ── Modifier les infos personnelles ─────────────────────────────────────
    #[Route('/modifier', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $nom        = trim($request->request->get('nom'));
            $prenom     = trim($request->request->get('prenom'));
            $gsm        = trim($request->request->get('gsm'));
            $adresse    = trim($request->request->get('adresse'));
            $mdp        = $request->request->get('mdp');
            $mdpConfirm = $request->request->get('mdp_confirm');

            if (!$nom || !$prenom) {
                $this->addFlash('danger', 'Le nom et le prénom sont obligatoires.');
                return $this->redirectToRoute('app_profile_edit');
            }

            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setGsm($gsm ?: null);
            $user->setAdresse($adresse ?: null);

            // Changement de mot de passe optionnel
            if ($mdp) {
                if ($mdp !== $mdpConfirm) {
                    $this->addFlash('danger', 'Les mots de passe ne correspondent pas.');
                    return $this->redirectToRoute('app_profile_edit');
                }
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/', $mdp)) {
                    $this->addFlash('danger', 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
                    return $this->redirectToRoute('app_profile_edit');
                }
                $user->setMdpHash($hasher->hashPassword($user, $mdp));
            }

            $em->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
        ]);
    }

    // ── Modifier une commande ────────────────────────────────────────────────
    #[Route('/commandes/{id}/modifier', name: 'app_profile_commande_edit', methods: ['GET', 'POST'])]
    public function editCommande(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        if ($commande->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($commande->getStatut() !== 'EN_ATTENTE') {
            $this->addFlash('danger', 'Cette commande ne peut plus être modifiée.');
            return $this->redirectToRoute('app_profile');
        }

        if ($request->isMethod('POST')) {
            $adresse     = trim($request->request->get('adresse'));
            $dateStr     = $request->request->get('date_prestation');
            $heureStr    = $request->request->get('heure_prestation');
            $nbPersonnes = (int)$request->request->get('nb_personnes');

            if ($nbPersonnes < $commande->getMenu()->getNbPersonnesMin()) {
                $this->addFlash('danger', 'Le nombre de personnes est inférieur au minimum requis.');
                return $this->redirectToRoute('app_profile_commande_edit', ['id' => $commande->getId()]);
            }

            $commande->setAdresse($adresse);
            $commande->setNbPersonnes($nbPersonnes);
            $commande->setDatePrestation(new \DateTimeImmutable($dateStr . ' ' . ($heureStr ?: '12:00')));

            // Recalcul prix
            $reduction = 0.00;
            if ($nbPersonnes >= ($commande->getMenu()->getNbPersonnesMin() + 5)) {
                $reduction = (float)$commande->getPrixMenu() * 0.10;
            }
            $fraisLivraison = (float)$commande->getFraisLivraison();
            $prixTotal      = (float)$commande->getPrixMenu() - $reduction + $fraisLivraison;

            $commande->setReduction(number_format($reduction, 2, '.', ''));
            $commande->setPrixTotal(number_format($prixTotal, 2, '.', ''));

            $em->flush();
            $this->addFlash('success', 'Commande modifiée avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/commande_edit.html.twig', [
            'commande' => $commande,
        ]);
    }

    // ── Laisser un avis ─────────────────────────────────────────────────────
    #[Route('/commandes/{id}/avis', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function newAvis(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        if ($commande->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }
        if ($commande->getStatut() !== 'TERMINEE') {
            $this->addFlash('danger', 'Vous ne pouvez laisser un avis que sur une commande terminée.');
            return $this->redirectToRoute('app_profile');
        }
        if ($commande->getAvis() !== null) {
            $this->addFlash('danger', 'Vous avez déjà laissé un avis pour cette commande.');
            return $this->redirectToRoute('app_profile');
        }

        if ($request->isMethod('POST')) {
            $note        = (int)$request->request->get('note');
            $commentaire = trim($request->request->get('commentaire'));

            if ($note < 1 || $note > 5) {
                $this->addFlash('danger', 'La note doit être entre 1 et 5.');
                return $this->redirectToRoute('app_avis_new', ['id' => $commande->getId()]);
            }
            if (!$commentaire) {
                $this->addFlash('danger', 'Le commentaire est obligatoire.');
                return $this->redirectToRoute('app_avis_new', ['id' => $commande->getId()]);
            }

            $avis = new Avis();
            $avis->setCommande($commande);
            $avis->setUtilisateur($user);
            $avis->setNote($note);
            $avis->setCommentaire($commentaire);
            $avis->setStatut('EN_ATTENTE');

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été soumis et sera visible après validation.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/avis_new.html.twig', [
            'commande' => $commande,
        ]);
    }
}