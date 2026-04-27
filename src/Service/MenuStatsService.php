<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use DateTime;

class MenuStatsService
{
    private Collection $collection;

    public function __construct(string $mongoUri)
    {
        $client = new Client($mongoUri);
        $database = $client->selectDatabase('vite_gourmand');
        $this->collection = $database->selectCollection('menu_stats');
    }

    /**
     * Récupère toutes les statistiques
     */
    public function getAllStats(): array
    {
        $stats = $this->collection
            ->find([], ['sort' => ['totalOrders' => -1]])
            ->toArray();

        return array_map($this->documentToArray(), $stats);
    }

    /**
     * Récupère les stats d'un menu spécifique
     */
    public function getMenuStats(int $idMenu): ?array
    {
        $doc = $this->collection->findOne(['idMenu' => $idMenu]);
        return $doc ? $this->documentToArray()($doc) : null;
    }

    /**
     * Met à jour les statistiques d'un menu
     * (appelé après chaque changement de commande)
     */
    public function updateMenuStats(int $idMenu, string $titleMenu, int $totalOrders, float $totalRevenue, ?float $averageRating = null): void
    {
        $this->collection->updateOne(
            ['idMenu' => $idMenu],
            [
                '$set' => [
                    'idMenu' => $idMenu,
                    'titleMenu' => $titleMenu,
                    'totalOrders' => $totalOrders,
                    'totalRevenue' => $totalRevenue,
                    'averageRating' => $averageRating ?? 0.0,
                    'lastUpdate' => new DateTime(),
                ]
            ],
            ['upsert' => true]
        );
    }

    /**
     * Incrémente le nombre de commandes pour un menu
     */
    public function incrementOrderCount(int $idMenu, float $orderPrice): void
    {
        $this->collection->updateOne(
            ['idMenu' => $idMenu],
            [
                '$inc' => [
                    'totalOrders' => 1,
                    'totalRevenue' => $orderPrice,
                ],
                '$set' => ['lastUpdate' => new DateTime()]
            ]
        );
    }

    /**
     * Décrémente le nombre de commandes (annulation)
     */
    public function decrementOrderCount(int $idMenu, float $orderPrice): void
    {
        $this->collection->updateOne(
            ['idMenu' => $idMenu],
            [
                '$inc' => [
                    'totalOrders' => -1,
                    'totalRevenue' => -$orderPrice,
                ],
                '$set' => ['lastUpdate' => new DateTime()]
            ]
        );
    }

    /**
     * Met à jour la note moyenne d'un menu
     */
    public function updateAverageRating(int $idMenu): void
    {
        // Cette méthode sera appelée après validation d'un avis
        // La calcul de la moyenne sera fait depuis la base SQL
        // puis envoyé en paramètre
    }

    /**
     * Récupère les données formatées pour le graphique
     */
    public function getDataForChart(): array
    {
        $stats = $this->getAllStats();

        return array_map(fn($stat) => [
            'title' => $stat['titleMenu'],
            'orders' => $stat['totalOrders'],
            'revenue' => round($stat['totalRevenue'], 2),
            'rating' => $stat['averageRating'] ?? 0,
        ], $stats);
    }

    /**
     * Récupère les stats pour les 5 menus les plus commandés
     */
    public function getTopMenus(int $limit = 5): array
    {
        $stats = $this->collection
            ->find([], ['sort' => ['totalOrders' => -1], 'limit' => $limit])
            ->toArray();

        return array_map($this->documentToArray(), $stats);
    }

    /**
     * Récupère les stats pour les 5 menus les plus rentables
     */
    public function getTopMenusByRevenue(int $limit = 5): array
    {
        $stats = $this->collection
            ->find([], ['sort' => ['totalRevenue' => -1], 'limit' => $limit])
            ->toArray();

        return array_map($this->documentToArray(), $stats);
    }

    /**
     * Réinitialise les stats (utile pour tests ou réinitialisation)
     */
    public function resetStats(int $idMenu): void
    {
        $this->collection->updateOne(
            ['idMenu' => $idMenu],
            [
                '$set' => [
                    'totalOrders' => 0,
                    'totalRevenue' => 0.0,
                    'averageRating' => 0.0,
                    'lastUpdate' => new DateTime(),
                ]
            ]
        );
    }

    /**
     * Supprime toutes les stats
     */
    public function deleteAllStats(): void
    {
        $this->collection->deleteMany([]);
    }

    /**
     * Crée un index sur idMenu pour les performances
     */
    public function createIndexes(): void
    {
        $this->collection->createIndex(['idMenu' => 1], ['unique' => true]);
        $this->collection->createIndex(['totalOrders' => -1]);
        $this->collection->createIndex(['totalRevenue' => -1]);
    }

    /**
     * Convertit un document MongoDB en array PHP
     */
    private function documentToArray(): callable
    {
        return function (BSONDocument $doc): array {
            return [
                '_id' => (string)$doc['_id'],
                'idMenu' => $doc['idMenu'] ?? null,
                'titleMenu' => $doc['titleMenu'] ?? '',
                'totalOrders' => $doc['totalOrders'] ?? 0,
                'totalRevenue' => $doc['totalRevenue'] ?? 0.0,
                'averageRating' => $doc['averageRating'] ?? 0.0,
                'lastUpdate' => $doc['lastUpdate'] instanceof DateTime ? $doc['lastUpdate'] : new DateTime(),
            ];
        };
    }
}
