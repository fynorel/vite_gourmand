<?php
namespace App\Controller;

use App\Entity\Allergene;
use App\Entity\Plat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/plats-management')]
#[IsGranted('ROLE_EMPLOYE')]
class PlatManagementController extends AbstractController
{
    #[Route('', name: 'app_plat_management_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $plats = $em->getRepository(Plat::class)->findAll();

        return $this->render('plat_management/list.html.twig', [
            'plats' => $plats,
        ]);
    }

    #[Route('/new', name: 'app_plat_management_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $allergenes = $em->getRepository(Allergene::class)->findAll();

        if ($request->isMethod('POST')) {
            $plat = new Plat();
            $plat->setNom($request->request->get('nom'));
            $plat->setType($request->request->get('type'));
            $plat->setDescription($request->request->get('description'));
            $plat->setActif(true); // un plat nouvellement créé est toujours actif

            // Associer les allergènes cochés
            $allergenesIds = $request->request->all('allergenes'); // tableau d'ids
            foreach ($allergenesIds as $id) {
                $allergene = $em->getRepository(Allergene::class)->find($id);
                if ($allergene) {
                    $plat->addAllergene($allergene);
                }
            }

            $em->persist($plat);
            $em->flush();

            $this->addFlash('success', 'Plat créé avec succès');

            return $this->redirectToRoute('app_plat_management_list');
        }

        return $this->render('plat_management/new.html.twig', [
            'allergenes' => $allergenes,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plat_management_edit', methods: ['GET', 'POST'])]
    public function edit(Plat $plat, Request $request, EntityManagerInterface $em): Response
    {
        $allergenes = $em->getRepository(Allergene::class)->findAll();

        if ($request->isMethod('POST')) {
            $plat->setNom($request->request->get('nom'));
            $plat->setType($request->request->get('type'));
            $plat->setDescription($request->request->get('description'));
            $plat->setActif($request->request->has('actif'));

            // Réinitialiser les allergènes puis ré-associer les cochés
            foreach ($plat->getAllergenes() as $allergene) {
                $plat->removeAllergene($allergene);
            }

            $allergenesIds = $request->request->all('allergenes');
            foreach ($allergenesIds as $id) {
                $allergene = $em->getRepository(Allergene::class)->find($id);
                if ($allergene) {
                    $plat->addAllergene($allergene);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Plat modifié avec succès');

            return $this->redirectToRoute('app_plat_management_list');
        }

        return $this->render('plat_management/edit.html.twig', [
            'plat'       => $plat,
            'allergenes' => $allergenes,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_plat_management_delete', methods: ['POST'])]
    public function delete(Plat $plat, EntityManagerInterface $em): Response
    {
        $em->remove($plat);
        $em->flush();

        $this->addFlash('success', 'Plat supprimé');

        return $this->redirectToRoute('app_plat_management_list');
    }
}