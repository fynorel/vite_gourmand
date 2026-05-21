<?php
namespace App\Controller;

use App\Entity\Horaire;
use App\Repository\EntrepriseRepository;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/horaires')]
#[IsGranted('ROLE_EMPLOYE')]
class HoraireController extends AbstractController
{
    #[Route('', name: 'app_horaire_list', methods: ['GET'])]
    public function list(EntrepriseRepository $entrepriseRepo): Response
    {
        $entreprise = $entrepriseRepo->find(1);
        $horaires   = [];

        foreach ($entreprise->getHoraires() as $horaire) {
            $horaires[$horaire->getJourSemaine()] = $horaire;
        }
        ksort($horaires);

        return $this->render('horaire/list.html.twig', [
            'horaires'   => $horaires,
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_horaire_edit', methods: ['GET', 'POST'])]
    public function edit(
        Horaire $horaire,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($request->isMethod('POST')) {
            $estFerme = $request->request->has('est_ferme');
            $horaire->setEstFerme($estFerme);

            if (!$estFerme) {
                $ouverture  = $request->request->get('heure_ouverture');
                $fermeture  = $request->request->get('heure_fermeture');
                $horaire->setHeureOuverture($ouverture ? new \DateTimeImmutable($ouverture) : null);
                $horaire->setHeureFermeture($fermeture ? new \DateTimeImmutable($fermeture) : null);
            } else {
                $horaire->setHeureOuverture(null);
                $horaire->setHeureFermeture(null);
            }

            $em->flush();
            $this->addFlash('success', 'Horaire mis à jour.');
            return $this->redirectToRoute('app_horaire_list');
        }

        return $this->render('horaire/edit.html.twig', [
            'horaire' => $horaire,
        ]);
    }
}