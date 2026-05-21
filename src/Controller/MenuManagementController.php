<?php
namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/menus-management')]
#[IsGranted('ROLE_EMPLOYE')]
class MenuManagementController extends AbstractController
{
    #[Route('', name: 'app_menu_management_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $menus = $em->getRepository(Menu::class)->findAll();

        return $this->render('menu_management/list.html.twig', [
            'menus' => $menus,
        ]);
    }

    /**
     * Groupe les plats actifs par type : ['entree' => [...], 'plat' => [...], 'dessert' => [...]]
     */
    private function getPlatsByType(EntityManagerInterface $em): array
    {
        $plats = $em->getRepository(Plat::class)->findBy(['actif' => true], ['nom' => 'ASC']);

        $grouped = ['ENTREE' => [], 'PLAT' => [], 'DESSERT' => []];
        foreach ($plats as $plat) {
            $type = $plat->getType();
            if (array_key_exists($type, $grouped)) {
                $grouped[$type][] = $plat;
            }
        }

        return $grouped;
    }

    #[Route('/new', name: 'app_menu_management_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $platsByType = $this->getPlatsByType($em);

        if ($request->isMethod('POST')) {
            $menu = new Menu();
            $menu->setTitre($request->request->get('titre'));
            $menu->setDescription($request->request->get('description'));
            $menu->setTheme($request->request->get('theme'));
            $menu->setRegime($request->request->get('regime'));
            $menu->setNbPersonnesMin((int)$request->request->get('nbPersonnesMin'));
            $menu->setPrix((float)$request->request->get('prix'));
            $menu->setStock((int)$request->request->get('stock'));
            $menu->setConditions($request->request->get('conditions'));
            $menu->setActif(true);
            $menu->setDateCreation(new \DateTimeImmutable());

            $platsIds = $request->request->all('plats');
            foreach ($platsIds as $id) {
                $plat = $em->getRepository(Plat::class)->find($id);
                if ($plat) {
                    $menu->addPlat($plat);
                }
            }

            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu créé avec succès');
            return $this->redirectToRoute('app_menu_management_list');
        }

        return $this->render('menu_management/new.html.twig', [
            'platsByType' => $platsByType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_menu_management_edit', methods: ['GET', 'POST'])]
    public function edit(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        $platsByType = $this->getPlatsByType($em);

        if ($request->isMethod('POST')) {
            $menu->setTitre($request->request->get('titre'));
            $menu->setDescription($request->request->get('description'));
            $menu->setTheme($request->request->get('theme'));
            $menu->setRegime($request->request->get('regime'));
            $menu->setNbPersonnesMin((int)$request->request->get('nbPersonnesMin'));
            $menu->setPrix((float)$request->request->get('prix'));
            $menu->setStock((int)$request->request->get('stock'));
            $menu->setConditions($request->request->get('conditions'));
            $menu->setActif($request->request->has('actif'));

            foreach ($menu->getPlats() as $plat) {
                $menu->removePlat($plat);
            }

            $platsIds = $request->request->all('plats');
            foreach ($platsIds as $id) {
                $plat = $em->getRepository(Plat::class)->find($id);
                if ($plat) {
                    $menu->addPlat($plat);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Menu modifié avec succès');
            return $this->redirectToRoute('app_menu_management_list');
        }

        return $this->render('menu_management/edit.html.twig', [
            'menu'        => $menu,
            'platsByType' => $platsByType,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_menu_management_delete', methods: ['POST'])]
    public function delete(Menu $menu, EntityManagerInterface $em): Response
    {
        $em->remove($menu);
        $em->flush();

        $this->addFlash('success', 'Menu supprimé');
        return $this->redirectToRoute('app_menu_management_list');
    }
}
