<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
    * Récupère les menus actifs avec le nombre de plats
    */
    public function findActiveMenus(): array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_menus_actifs')
            ->fetchAllAssociative();
    }

    /**
    * Récupère les notes moyennes par menu
    */
    public function findMenuRatings(): array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_note_moyenne_menu')
            ->fetchAllAssociative();
    }

    /**
    * Récupère la note moyenne pour un menu spécifique
    */
    public function findMenuRating(int $menuId): ?array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_note_moyenne_menu WHERE id_menu = ?', [$menuId])
            ->fetchAssociative() ?: null;
    }
}