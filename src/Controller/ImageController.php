<?php

namespace App\Controller;

use App\Entity\ImageMenu;
use App\Entity\Menu;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin/images')]
#[IsGranted('ROLE_ADMIN')]
class ImageController extends AbstractController
{
    #[Route('/upload', name: 'app_image_upload', methods: ['POST'])]
    public function upload(
        Request $request,
        ImageUploadService $imageUploadService,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $file = $request->files->get('image');
        $menuId = $request->request->get('menu_id');
        $alt = $request->request->get('alt', 'Image du menu');

        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier'], 400);
        }

        if (!$menuId) {
            return new JsonResponse(['error' => 'Menu ID manquant'], 400);
        }

        $menu = $em->getRepository(Menu::class)->find($menuId);
        if (!$menu) {
            return new JsonResponse(['error' => 'Menu non trouvé'], 404);
        }

        try {
            $path = $imageUploadService->uploadImage($file, 'menus');

            // Sauvegarder en BD
            $image = new ImageMenu();
            $image->setUrl($path);
            $image->setAlt($alt);
            $image->setMenu($menu);
            $image->setOrdre(1);

            $em->persist($image);
            $em->flush();

            return new JsonResponse(['path' => $path, 'id' => $image->getId()]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/list/{menuId}', name: 'app_image_list', methods: ['GET'])]
    public function listImages(int $menuId, EntityManagerInterface $em): JsonResponse
    {
        $images = $em->getRepository(ImageMenu::class)->findBy(
            ['menu' => $menuId],
            ['ordre' => 'ASC']
        );

        $data = [];
        foreach ($images as $img) {
            $data[] = [
                'id' => $img->getId(),
                'url' => $img->getUrl(),
                'alt' => $img->getAlt()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/delete/{id}', name: 'app_image_delete', methods: ['DELETE'])]
    public function deleteImage(
        int $id,
        EntityManagerInterface $em,
        ImageUploadService $imageUploadService
    ): JsonResponse
    {
        $image = $em->getRepository(ImageMenu::class)->find($id);
        if (!$image) {
            return new JsonResponse(['error' => 'Image non trouvée'], 404);
        }

        $imageUploadService->deleteImage($image->getUrl());
        $em->remove($image);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('', name: 'app_admin_images', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/images/menu.html.twig');
    }
}
