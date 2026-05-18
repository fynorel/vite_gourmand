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
        $sql = 'SELECT COUNT(*) as total FROM commande';
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAllAssociative();
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
}
