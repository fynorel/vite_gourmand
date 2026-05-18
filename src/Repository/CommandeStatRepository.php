<?php

namespace App\Repository;

use App\Document\CommandeStat;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class CommandeStatRepository extends DocumentRepository
{
    public function findStatsByMenu(int $menuId)
    {
        return $this->createQueryBuilder()
            ->field('menuId')->equals($menuId)
            ->sort('date', 'desc')
            ->getQuery()
            ->execute();
    }

    public function findAllStats()
    {
        return $this->createQueryBuilder()
            ->sort('date', 'desc')
            ->getQuery()
            ->execute();
    }

    public function getStatsForChart()
    {
        return $this->createQueryBuilder()
            ->select('menuTitre', 'nombreCommandes', 'prixTotal')
            ->sort('nombreCommandes', 'desc')
            ->limit(10)
            ->getQuery()
            ->execute();
    }
}
