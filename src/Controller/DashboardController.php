<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\HistoriqueStatut;
use App\Repository\AvisRepository;
use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_EMPLOYE')]
class DashboardController extends AbstractController
{
    // Statuts autorisés et leur ordre logique
    private const STATUTS = [
        'EN_ATTENTE',
        'ACCEPTE',
        'EN_PREPARATION',
        'EN_COURS_LIVRAISON',
        'LIVRE',
        'EN_ATTENTE_RETOUR_MATERIEL',
        'TERMINEE',
        'ANNULEE',
    ];

    #[Route('', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(
        CommandeRepository $commandeRepo,
        MenuRepository $menuRepo,
        AvisRepository $avisRepo
    ): Response {
        $dashboardData = $commandeRepo->getDashboardData();
        $menus         = $menuRepo->findActiveMenus();
        $avis          = $avisRepo->findPublishedReviews();

        return $this->render('dashboard/index.html.twig', [
            'dashboard' => $dashboardData,
            'menus'     => $menus,
            'avis'      => $avis,
        ]);
    }

    #[Route('/commandes', name: 'app_dashboard_commandes', methods: ['GET'])]
    public function commandes(CommandeRepository $commandeRepo, Request $request): Response
    {
        $statut = $request->query->get('statut');
        $search = $request->query->get('search');

        $commandes = $commandeRepo->findWithFilters($statut, $search);

        return $this->render('dashboard/commandes.html.twig', [
            'commandes'      => $commandes,
            'statut_filter'  => $statut,
            'search_filter'  => $search,
            'statuts'        => self::STATUTS,
        ]);
    }

    #[Route('/commandes/{id}/statut', name: 'app_dashboard_commande_statut', methods: ['POST'])]
    public function updateStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        if (!$this->isCsrfTokenValid('statut_' . $commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $nouveauStatut = $request->request->get('statut');

        if (!in_array($nouveauStatut, self::STATUTS)) {
            $this->addFlash('danger', 'Statut invalide.');
            return $this->redirectToRoute('app_dashboard_commandes');
        }

        $ancienStatut = $commande->getStatut();
        $commande->setStatut($nouveauStatut);

        // Historique du changement de statut
        $historique = new HistoriqueStatut();
        $historique->setCommande($commande);
        $historique->setStatut($nouveauStatut);
        $historique->setChangedBy($this->getUser());
        $historique->setChangedAt(new \DateTimeImmutable());
        $em->persist($historique);

        // Gestion spécifique selon le statut
        $user = $commande->getUtilisateur();

        // Statut TERMINEE → notifier l'utilisateur pour laisser un avis
        if ($nouveauStatut === 'TERMINEE') {
            $email = (new Email())
                ->from('noreply@vitegourmand.fr')
                ->to($user->getMail())
                ->subject('Votre commande est terminée - Laissez votre avis !')
                ->html(
                    '<h2>Bonjour ' . htmlspecialchars($user->getPrenom()) . ',</h2>' .
                    '<p>Votre commande <strong>' . htmlspecialchars($commande->getMenu()->getTitre()) . '</strong> est terminée.</p>' .
                    '<p>Nous espérons que vous avez apprécié notre prestation. Connectez-vous à votre espace pour laisser un avis !</p>' .
                    '<p>L\'équipe Vite & Gourmand</p>'
                );
            $mailer->send($email);
        }

        // Statut EN_ATTENTE_RETOUR_MATERIEL → notifier l'utilisateur
        if ($nouveauStatut === 'EN_ATTENTE_RETOUR_MATERIEL') {
            $email = (new Email())
                ->from('noreply@vitegourmand.fr')
                ->to($user->getMail())
                ->subject('Retour de matériel - Vite & Gourmand')
                ->html(
                    '<h2>Bonjour ' . htmlspecialchars($user->getPrenom()) . ',</h2>' .
                    '<p>Nous vous rappelons que du matériel vous a été prêté lors de votre prestation.</p>' .
                    '<p><strong>Vous disposez de 10 jours ouvrés pour le restituer.</strong></p>' .
                    '<p>Sans restitution dans ce délai, des frais de <strong>600 €</strong> vous seront facturés conformément aux conditions générales de vente.</p>' .
                    '<p>Pour organiser le retour, contactez-nous directement.</p>' .
                    '<p>L\'équipe Vite & Gourmand</p>'
                );
            $mailer->send($email);
        }

        $em->flush();

        $this->addFlash('success', 'Statut mis à jour : ' . $nouveauStatut);
        return $this->redirectToRoute('app_dashboard_commandes');
    }

    #[Route('/commandes/{id}/annuler', name: 'app_dashboard_commande_annuler', methods: ['POST'])]
    public function annulerCommande(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('annuler_' . $commande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $modeContact = $request->request->get('mode_contact');
        $motif       = $request->request->get('motif');

        if (!$modeContact || !$motif) {
            $this->addFlash('danger', 'Le mode de contact et le motif sont obligatoires.');
            return $this->redirectToRoute('app_dashboard_commandes');
        }

        $commande->setStatut('ANNULEE');
        $commande->setModeContactAnnul($modeContact);
        $commande->setMotifAnnulation($motif);
        $commande->setDateContactAnnul(new \DateTimeImmutable());
        $commande->setAnnulePar($this->getUser());

        // Remettre le stock
        $commande->getMenu()->setStock($commande->getMenu()->getStock() + 1);

        // Historique
        $historique = new HistoriqueStatut();
        $historique->setCommande($commande);
        $historique->setStatut('ANNULEE');
        $historique->setChangedBy($this->getUser());
        $historique->setCommentaire($modeContact . ' : ' . $motif);
        $em->persist($historique);

        $em->flush();

        $this->addFlash('success', 'Commande annulée.');
        return $this->redirectToRoute('app_dashboard_commandes');
    }

    #[Route('/menus', name: 'app_dashboard_menus', methods: ['GET'])]
    public function menus(MenuRepository $menuRepo): Response
    {
        $menus   = $menuRepo->findActiveMenus();
        $ratings = $menuRepo->findMenuRatings();

        return $this->render('dashboard/menus.html.twig', [
            'menus'   => $menus,
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