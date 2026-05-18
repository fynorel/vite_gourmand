<?php

namespace App\Service;

use App\Document\CommandeStat;
use App\Entity\Commande;
use Doctrine\ODM\MongoDB\DocumentManager;
use DateTime;

class CommandeStatService
{
    public function __construct(private DocumentManager $dm)
    {
    }

    /**
     * Enregistrer une commande dans MongoDB
     */
    public function recordCommandeStat(Commande $commande): void
    {
        $stat = new CommandeStat();
        $stat->setMenuId($commande->getMenu()->getId());
        $stat->setMenuTitre($commande->getMenu()->getTitre());
        $stat->setDate(new DateTime());
        $stat->setNombreCommandes(1);
        $stat->setPrixTotal($commande->getPrixTotal());
        $stat->setDateCreation(new DateTime());

        $this->dm->persist($stat);
        $this->dm->flush();
    }

    /**
     * Récupérer les stats pour les graphiques
     */
    public function getStatsForChart(): array
    {
        $qb = $this->dm->createAggregationBuilder(CommandeStat::class);
        
        $stats = $qb
            ->group()
            ->field('_id')->expression('$menuTitre')
            ->field('total')->sum('$nombreCommandes')
            ->field('prix')->sum('$prixTotal')
            ->sort(['total' => -1])
            ->limit(10)
            ->execute();

        return iterator_to_array($stats);
    }

    /**
     * Récupérer les stats par date
     */
    public function getStatsByDate(): array
    {
        $qb = $this->dm->createAggregationBuilder(CommandeStat::class);
        
        $stats = $qb
            ->group()
            ->field('_id')->expression('$date')
            ->field('total')->sum('$nombreCommandes')
            ->sort(['_id' => 1])
            ->execute();

        return iterator_to_array($stats);
    }

    /**
     * Récupérer tous les menus et leurs stats
     */
    public function getAllMenuStats(): array
    {
        $qb = $this->dm->createQueryBuilder(CommandeStat::class);
        
        return $qb
            ->select('menuTitre', 'nombreCommandes', 'prixTotal')
            ->sort('dateCreation', 'desc')
            ->limit(20)
            ->getQuery()
            ->execute()
            ->toArray();
    }
}
