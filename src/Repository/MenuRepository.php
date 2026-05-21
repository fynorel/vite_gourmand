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
     * Récupère les menus actifs sans filtre (page d'accueil)
     */
    public function findActiveMenus(): array
    {
        return $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT * FROM v_menus_actifs')
            ->fetchAllAssociative();
    }

    /**
     * Récupère les menus actifs avec filtres dynamiques (vue globale)
     */
    public function findActiveMenusWithFilters(
        ?string $theme,
        ?string $regime,
        ?float  $prixMax,
        ?float  $prixMin,
        ?float  $prixMaxRange,
        ?int    $nbPersonnes
    ): array {
        $sql    = 'SELECT * FROM v_menus_actifs WHERE 1=1';
        $params = [];

        if ($theme) {
            $sql     .= ' AND theme = :theme';
            $params['theme'] = $theme;
        }

        if ($regime) {
            $sql     .= ' AND regime = :regime';
            $params['regime'] = $regime;
        }

        if ($prixMax !== null) {
            $sql     .= ' AND prix <= :prixMax';
            $params['prixMax'] = $prixMax;
        }

        // Fourchette de prix (min + max)
        if ($prixMin !== null) {
            $sql     .= ' AND prix >= :prixMin';
            $params['prixMin'] = $prixMin;
        }

        if ($prixMaxRange !== null) {
            $sql     .= ' AND prix <= :prixMaxRange';
            $params['prixMaxRange'] = $prixMaxRange;
        }

        if ($nbPersonnes !== null) {
            $sql     .= ' AND nb_personnes_min <= :nbPersonnes';
            $params['nbPersonnes'] = $nbPersonnes;
        }

        $sql .= ' ORDER BY prix ASC';

        return $this->getEntityManager()->getConnection()
            ->executeQuery($sql, $params)
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