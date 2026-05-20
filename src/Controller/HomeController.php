<?php
namespace App\Controller;

use App\Repository\AvisRepository;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(MenuRepository $menuRepo, AvisRepository $avisRepo): Response
    {
        $menus = $menuRepo->findActiveMenus();
        $avis  = $avisRepo->findPublishedReviews();

        return $this->render('home/index.html.twig', [
            'menus' => $menus,
            'avis'  => $avis,
        ]);
    }

    #[Route('/apropos', name: 'app_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $titre       = $request->request->get('titre');
            $description = $request->request->get('description');
            $mailExpediteur = $request->request->get('mail');

            // Validation simple
            if (!$titre || !$description || !filter_var($mailExpediteur, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('danger', 'Veuillez remplir tous les champs correctement.');
                return $this->redirectToRoute('app_contact');
            }

            // Envoi du mail à l'entreprise
            $email = (new Email())
                ->from($mailExpediteur)
                ->to('contact@vitegourmand.fr') // ← adresse mail de l'entreprise
                ->replyTo($mailExpediteur)
                ->subject('[Contact] ' . $titre)
                ->html(
                    '<p><strong>De :</strong> ' . htmlspecialchars($mailExpediteur) . '</p>' .
                    '<p><strong>Sujet :</strong> ' . htmlspecialchars($titre) . '</p>' .
                    '<hr>' .
                    '<p>' . nl2br(htmlspecialchars($description)) . '</p>'
                );

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('home/contact.html.twig');
    }
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        return $this->render('pages/mentions_legales.html.twig');
    }

    #[Route('/conditions-generales-de-vente', name: 'app_cgv', methods: ['GET'])]
    public function cgv(): Response
    {
        return $this->render('pages/cgv.html.twig');
    }


}