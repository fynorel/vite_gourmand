<?php

namespace App\EventListener;

use App\Entity\Commande;
use App\Repository\AvisRepository;
use App\Service\MenuStatsService;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::postPersist, priority: 500)]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500)]
#[AsDoctrineListener(event: Events::postRemove, priority: 500)]
class CommandeListener
{
    public function __construct(
        private MenuStatsService $statsService,
        private AvisRepository $avisRepository,
    ) {}

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Commande) {
            return;
        }

        // Mettre à jour les stats pour ce menu
        $this->updateMenuStats($entity);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Commande) {
            return;
        }

        // Mettre à jour les stats (cas : statut changé, prix modifié, etc.)
        $this->updateMenuStats($entity);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Commande) {
            return;
        }

        // Décrémente les stats si la commande est annulée
        if ($entity->getStatut()->value === 'ANNULEE') {
            $this->statsService->decrementOrderCount(
                $entity->getMenu()->getId(),
                $entity->getPrixTotal()
            );
        }
    }

    /**
     * Met à jour les statistiques MongoDB pour un menu
     */
    private function updateMenuStats(Commande $commande): void
    {
        $menu = $commande->getMenu();

        // Récupérer les stats actuelles de ce menu depuis la base SQL
        $totalOrders = $this->getTotalOrdersForMenu($menu->getId());
        $totalRevenue = $this->getTotalRevenueForMenu($menu->getId());
        $averageRating = $this->getAverageRatingForMenu($menu->getId());

        // Mettre à jour MongoDB
        $this->statsService->updateMenuStats(
            $menu->getId(),
            $menu->getTitre(),
            $totalOrders,
            $totalRevenue,
            $averageRating
        );
    }

    /**
     * Récupère le nombre total de commandes acceptées/livrées pour un menu
     */
    private function getTotalOrdersForMenu(int $menuId): int
    {
        // À implémenter selon ton repository
        // SELECT COUNT(*) FROM commande WHERE id_menu = ? AND statut IN ('ACCEPTE', 'LIVRE', 'TERMINEE')
        return 0; // À remplacer
    }

    /**
     * Récupère le revenu total pour un menu
     */
    private function getTotalRevenueForMenu(int $menuId): float
    {
        // À implémenter selon ton repository
        // SELECT SUM(prix_total) FROM commande WHERE id_menu = ? AND statut IN ('ACCEPTE', 'LIVRE', 'TERMINEE')
        return 0.0; // À remplacer
    }

    /**
     * Récupère la note moyenne pour un menu
     */
    private function getAverageRatingForMenu(int $menuId): ?float
    {
        // À implémenter selon ton repository
        // SELECT AVG(a.note) FROM avis a JOIN commande c ON c.id_commande = a.id_commande WHERE c.id_menu = ? AND a.statut = 'VALIDE'
        return null; // À remplacer
    }
}
