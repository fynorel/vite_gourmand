<?php

namespace App\Command;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Service\MenuStatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-menu-stats',
    description: 'Synchronise les statistiques des menus avec MongoDB',
    aliases: ['app:stats:sync'],
    hidden: false,
)]
class SyncMenuStatsCommand extends Command
{
    public function __construct(
        private MenuRepository $menuRepository,
        private MenuStatsService $statsService,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Cette commande synchronise les données de commandes et avis depuis MySQL vers MongoDB.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Synchronisation des statistiques menu vers MongoDB');

        try {
            // Créer les indexes si besoin
            $io->info('Création des indexes MongoDB...');
            $this->statsService->createIndexes();
            $io->success('Indexes créés');

            // Récupérer tous les menus
            $menus = $this->menuRepository->findAll();
            $totalMenus = count($menus);

            if ($totalMenus === 0) {
                $io->warning('Aucun menu trouvé dans la base de données.');
                return Command::SUCCESS;
            }

            $io->info("Traitement de {$totalMenus} menu(s)...");

            $progressBar = $io->createProgressBar($totalMenus);
            $progressBar->start();

            foreach ($menus as $menu) {
                $this->syncMenuStats($menu);
                $progressBar->advance();
            }

            $progressBar->finish();
            $io->newLine(2);

            $io->success("✅ Synchronisation complète ! {$totalMenus} menu(s) traité(s).");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Erreur lors de la synchronisation : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Synchronise un menu spécifique
     */
    private function syncMenuStats(Menu $menu): void
    {
        $menuId = $menu->getId();

        // Récupérer les statistiques depuis la base SQL
        $totalOrders = $this->getTotalOrdersForMenu($menuId);
        $totalRevenue = $this->getTotalRevenueForMenu($menuId);
        $averageRating = $this->getAverageRatingForMenu($menuId);

        // Mettre à jour MongoDB
        $this->statsService->updateMenuStats(
            $menuId,
            $menu->getTitre(),
            $totalOrders,
            $totalRevenue,
            $averageRating
        );
    }

    /**
     * Récupère le nombre total de commandes valides pour un menu
     */
    private function getTotalOrdersForMenu(int $menuId): int
    {
        $result = $this->em->createQuery(
            'SELECT COUNT(c.id) as total
             FROM App\Entity\Commande c
             WHERE c.menu = :menuId
             AND c.statut IN (\'ACCEPTE\', \'EN_PREPARATION\', \'EN_COURS_LIVRAISON\', \'LIVRE\', \'TERMINEE\')'
        )
            ->setParameter('menuId', $menuId)
            ->getSingleScalarResult();

        return (int)$result;
    }

    /**
     * Récupère le revenu total pour un menu
     */
    private function getTotalRevenueForMenu(int $menuId): float
    {
        $result = $this->em->createQuery(
            'SELECT SUM(c.prixTotal) as total
             FROM App\Entity\Commande c
             WHERE c.menu = :menuId
             AND c.statut IN (\'ACCEPTE\', \'EN_PREPARATION\', \'EN_COURS_LIVRAISON\', \'LIVRE\', \'TERMINEE\')'
        )
            ->setParameter('menuId', $menuId)
            ->getSingleScalarResult();

        return (float)($result ?? 0.0);
    }

    /**
     * Récupère la note moyenne pour un menu
     */
    private function getAverageRatingForMenu(int $menuId): ?float
    {
        $result = $this->em->createQuery(
            'SELECT AVG(a.note) as average
             FROM App\Entity\Avis a
             JOIN App\Entity\Commande c WITH c.id = a.commande
             WHERE c.menu = :menuId
             AND a.statut = \'VALIDE\''
        )
            ->setParameter('menuId', $menuId)
            ->getSingleScalarResult();

        return $result ? round((float)$result, 2) : null;
    }
}
