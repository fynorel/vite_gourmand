<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * Récupère les données du dashboard employé
     * (Compteurs de commandes par statut + avis à modérer)
     */
    public function getDashboardData(): array
    {
        $sql = '
            SELECT
                (SELECT COUNT(*) FROM commande WHERE statut = "EN_ATTENTE") AS nb_en_attente,
                (SELECT COUNT(*) FROM commande WHERE statut = "ACCEPTE") AS nb_acceptees,
                (SELECT COUNT(*) FROM commande WHERE statut = "EN_PREPARATION") AS nb_en_preparation,
                (SELECT COUNT(*) FROM commande WHERE statut = "EN_COURS_LIVRAISON") AS nb_en_livraison,
                (SELECT COUNT(*) FROM commande WHERE statut = "RETOUR_MATERIEL") AS nb_retour_materiel,
                (SELECT COUNT(*) FROM avis WHERE statut = "EN_ATTENTE") AS nb_avis_a_moderer
        ';

        return $this->getEntityManager()->getConnection()
            ->executeQuery($sql)
            ->fetchAssociative() ?: [];
    }

    /**
     * Récupère les commandes actives (en cours de traitement)
     */
    public function findActiveOrders(): array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_commandes_actives')
            ->fetchAllAssociative();
    }

    /**
     * Récupère les avis publiés
     */
    public function findPublishedReviews(): array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_avis_publies')
            ->fetchAllAssociative();
    }
}