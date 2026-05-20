<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use App\Service\CommandeStatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    // Tarifs livraison (CDC)
    private const FRAIS_BASE       = 5.00;
    private const FRAIS_PAR_KM     = 0.59;
    private const VILLE_BORDEAUX   = 'bordeaux';
    private const REDUCTION_TAUX   = 0.10;
    private const REDUCTION_ECART  = 5; // nb personnes en plus du min pour déclencher la réduction

    #[Route('', name: 'app_commande_list', methods: ['GET'])]
    public function list(CommandeRepository $commandeRepo): Response
    {
        $commandes = $commandeRepo->findByUtilisateur($this->getUser()->getId());

        return $this->render('commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        MenuRepository $menuRepo,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        CommandeStatService $statService
    ): Response {
        // Pré-sélection d'un menu depuis la vue détaillée
        $menuId = $request->query->get('menuId') ?? $request->request->get('menu_id');
        $menu   = $menuId ? $menuRepo->find($menuId) : null;
        $menus  = $menuRepo->findBy(['actif' => true]);
        $user   = $this->getUser();

        if ($request->isMethod('POST')) {
            $menuId      = $request->request->get('menu_id');
            $nbPersonnes = (int)$request->request->get('nb_personnes');
            $adresse     = trim($request->request->get('adresse'));
            $dateStr     = $request->request->get('date_prestation');
            $heureStr    = $request->request->get('heure_prestation');
            $distanceKm  = (float)$request->request->get('distance_km', 0);

            $menu = $menuRepo->find($menuId);

            if (!$menu) {
                $this->addFlash('danger', 'Menu introuvable.');
                return $this->redirectToRoute('app_menu_list');
            }

            // Vérification nb personnes minimum
            if ($nbPersonnes < $menu->getNbPersonnesMin()) {
                $this->addFlash('danger', 'Le nombre de personnes est inférieur au minimum requis (' . $menu->getNbPersonnesMin() . ').');
                return $this->redirectToRoute('app_commande_new', ['menuId' => $menu->getId()]);
            }

            // Vérification stock
            if ($menu->getStock() <= 0) {
                $this->addFlash('danger', 'Ce menu n\'est plus disponible.');
                return $this->redirectToRoute('app_menu_list');
            }

            // Calcul réduction (10% si nbPersonnes >= nbPersonnesMin + 5)
            $reduction = 0.00;
            if ($nbPersonnes >= ($menu->getNbPersonnesMin() + self::REDUCTION_ECART)) {
                $reduction = (float)$menu->getPrix() * self::REDUCTION_TAUX;
            }

            // Calcul frais de livraison
            $fraisLivraison = 0.00;
            if (stripos($adresse, self::VILLE_BORDEAUX) === false) {
                $fraisLivraison = self::FRAIS_BASE + ($distanceKm * self::FRAIS_PAR_KM);
            }

            // Calcul prix total
            $prixTotal = (float)$menu->getPrix() - $reduction + $fraisLivraison;

            // Date prestation
            $datePrestation = new \DateTimeImmutable($dateStr . ' ' . ($heureStr ?: '12:00'));

            // Création commande
            $commande = new Commande();
            $commande->setUtilisateur($user);
            $commande->setMenu($menu);
            $commande->setNbPersonnes($nbPersonnes);
            $commande->setAdresse($adresse);
            $commande->setDatePrestation($datePrestation);
            $commande->setPrixMenu((string)$menu->getPrix());
            $commande->setReduction(number_format($reduction, 2, '.', ''));
            $commande->setFraisLivraison(number_format($fraisLivraison, 2, '.', ''));
            $commande->setPrixTotal(number_format($prixTotal, 2, '.', ''));
            $commande->setStatut('EN_ATTENTE');

            // Décrémenter le stock
            $menu->setStock($menu->getStock() - 1);

            $em->persist($commande);
            $em->flush();
            // Enregistrer dans MongoDB pour les statistiques
            $statService->recordCommandeStat($commande);

            // Mail de confirmation
            $email = (new Email())
                ->from('noreply@vitegourmand.fr')
                ->to($user->getMail())
                ->subject('Confirmation de votre commande - Vite & Gourmand')
                ->html(
                    '<h2>Bonjour ' . htmlspecialchars($user->getPrenom()) . ',</h2>' .
                    '<p>Votre commande a bien été enregistrée !</p>' .
                    '<ul>' .
                    '<li><strong>Menu :</strong> ' . htmlspecialchars($menu->getTitre()) . '</li>' .
                    '<li><strong>Nombre de personnes :</strong> ' . $nbPersonnes . '</li>' .
                    '<li><strong>Date de prestation :</strong> ' . $datePrestation->format('d/m/Y à H:i') . '</li>' .
                    '<li><strong>Adresse :</strong> ' . htmlspecialchars($adresse) . '</li>' .
                    '<li><strong>Prix menu :</strong> ' . $menu->getPrix() . ' €</li>' .
                    ($reduction > 0 ? '<li><strong>Réduction (10%) :</strong> -' . number_format($reduction, 2) . ' €</li>' : '') .
                    ($fraisLivraison > 0 ? '<li><strong>Frais de livraison :</strong> ' . number_format($fraisLivraison, 2) . ' €</li>' : '') .
                    '<li><strong>Total :</strong> ' . number_format($prixTotal, 2) . ' €</li>' .
                    '</ul>' .
                    '<p>Nous vous contacterons prochainement pour confirmer votre commande.</p>' .
                    '<p>L\'équipe Vite & Gourmand</p>'
                );

            $mailer->send($email);

            $this->addFlash('success', 'Commande passée avec succès ! Un mail de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
        }

        return $this->render('commande/new.html.twig', [
            'menus'         => $menus,
            'menu_selected' => $menu,
            'user'          => $user,
        ]);
    }
    /**
     * Calcul dynamique du prix via AJAX
     */
    #[Route('/calcul-prix', name: 'app_commande_calcul_prix', methods: ['POST'])]
    public function calculPrix(Request $request, MenuRepository $menuRepo): JsonResponse
    {
        $menuId      = $request->request->get('menu_id');
        $nbPersonnes = (int)$request->request->get('nb_personnes', 0);
        $adresse     = trim($request->request->get('adresse', ''));
        $distanceKm  = (float)$request->request->get('distance_km', 0);

        $menu = $menuRepo->find($menuId);
        if (!$menu) {
            return new JsonResponse(['error' => 'Menu introuvable'], 404);
        }

        $reduction = 0.00;
        if ($nbPersonnes >= ($menu->getNbPersonnesMin() + self::REDUCTION_ECART)) {
            $reduction = (float)$menu->getPrix() * self::REDUCTION_TAUX;
        }

        $fraisLivraison = 0.00;
        if ($adresse && stripos($adresse, self::VILLE_BORDEAUX) === false) {
            $fraisLivraison = self::FRAIS_BASE + ($distanceKm * self::FRAIS_PAR_KM);
        }

        $prixTotal = (float)$menu->getPrix() - $reduction + $fraisLivraison;

        return new JsonResponse([
            'prix_menu'       => number_format((float)$menu->getPrix(), 2),
            'reduction'       => number_format($reduction, 2),
            'frais_livraison' => number_format($fraisLivraison, 2),
            'prix_total'      => number_format($prixTotal, 2),
            'nb_min'          => $menu->getNbPersonnesMin(),
        ]);
    }




    #[Route('/{id}', name: 'app_commande_detail', methods: ['GET'])]
    public function detail(Commande $commande): Response
    {
        if ($commande->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/annuler', name: 'app_commande_cancel', methods: ['POST'])]
    public function cancel(Commande $commande, EntityManagerInterface $em, Request $request): Response
    {
        if ($commande->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        if (!$this->isCsrfTokenValid('cancel_' . $commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        if ($commande->getStatut() === 'EN_ATTENTE') {
            $commande->setStatut('ANNULEE');
            // Remettre le stock
            $commande->getMenu()->setStock($commande->getMenu()->getStock() + 1);
            $em->flush();
            $this->addFlash('success', 'Commande annulée.');
        } else {
            $this->addFlash('danger', 'Impossible d\'annuler cette commande (statut : ' . $commande->getStatut() . ').');
        }

        return $this->redirectToRoute('app_commande_list');
    }

  
}