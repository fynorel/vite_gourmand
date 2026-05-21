<?php
namespace App\Twig;

use App\Repository\EntrepriseRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class HoraireExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private EntrepriseRepository $entrepriseRepo)
    {
    }

    public function getGlobals(): array
    {
        $entreprise = $this->entrepriseRepo->find(1);
        $horaires   = [];

        if ($entreprise) {
            foreach ($entreprise->getHoraires() as $horaire) {
                $horaires[$horaire->getJourSemaine()] = $horaire;
            }
            ksort($horaires);
        }

        return [
            'horaires_footer' => $horaires,
        ];
    }
}