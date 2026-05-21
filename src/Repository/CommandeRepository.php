<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

public function getDashboardData(): array
{
    $sql = 'SELECT 
        (SELECT COUNT(*) FROM commande WHERE statut = "EN_ATTENTE") AS nb_en_attente,
        (SELECT COUNT(*) FROM commande WHERE statut = "ACCEPTE") AS nb_acceptees,
        (SELECT COUNT(*) FROM commande WHERE statut = "EN_PREPARATION") AS nb_en_preparation,
        (SELECT COUNT(*) FROM commande WHERE statut = "EN_COURS_LIVRAISON") AS nb_en_livraison,
        (SELECT COUNT(*) FROM commande WHERE statut = "EN_ATTENTE_RETOUR_MATERIEL") AS nb_retour_materiel,
        (SELECT COUNT(*) FROM commande WHERE statut = "TERMINEE") AS nb_terminees,
        (SELECT COUNT(*) FROM commande WHERE statut = "ANNULEE") AS nb_annulees,
        (SELECT COUNT(*) FROM avis WHERE statut != "VALIDE") AS nb_avis_a_moderer
    ';
    
    return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAssociative() ?: [];
}

    public function findActiveOrders(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut NOT IN (:statuts)')
            ->setParameter('statuts', ['TERMINEE', 'ANNULEE'])
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.utilisateur = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateurId)
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithFilters(?string $statut, ?string $search): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->join('c.menu', 'm')
            ->orderBy('c.dateCreation', 'DESC');
 
        if ($statut) {
            $qb->andWhere('c.statut = :statut')
            ->setParameter('statut', $statut);
        }
 
        if ($search) {
            $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.mail LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }
 
        return $qb->getQuery()->getResult();
    }
 

}
