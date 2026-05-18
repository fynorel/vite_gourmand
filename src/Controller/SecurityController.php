<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Afficher les erreurs de validation
            if (!$form->isValid()) {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('app_register');
            }

        $data = $form->getData();
        $plainPassword = $form->get('password')->getData();

        try {
            // Vérifier que l'email n'existe pas
            $existingUser = $em->getRepository(Utilisateur::class)->findOneBy(['mail' => $data['mail']]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé');
                return $this->redirectToRoute('app_register');
            }

            // Créer l'utilisateur
            $user = new Utilisateur();
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setMail($data['mail']);
            $user->setGsm($data['gsm'] ?? null);
            $user->setAdresse($data['adresse'] ?? null);
            $user->setRole('UTILISATEUR');
            $user->setActif(true);

            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMdpHash($hashedPassword);

            // Sauvegarder
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé ! Connectez-vous maintenant');
            return $this->redirectToRoute('app_login');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
            return $this->redirectToRoute('app_register');
        }
    }

    return $this->render('security/register.html.twig', [
        'form' => $form,
    ]);
}

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('Cette méthode ne doit pas être appelée');
    }

    #[Route('/mon-compte', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('prenom');
            $nom = $request->request->get('nom');
            $gsm = $request->request->get('gsm');
            $adresse = $request->request->get('adresse');
            $newPassword = $request->request->get('password');

            if (!empty($prenom)) $user->setPrenom($prenom);
            if (!empty($nom)) $user->setNom($nom);
            if (!empty($gsm)) $user->setGsm($gsm);
            if (!empty($adresse)) $user->setAdresse($adresse);

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 10) {
                    $this->addFlash('error', 'Le mot de passe doit faire au moins 10 caractères');
                    return $this->redirectToRoute('app_profile');
                }

                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setMdpHash($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            if (empty($email)) {
                $this->addFlash('error', 'L\'email est obligatoire');
                return $this->redirectToRoute('app_forgot_password');
            }

            $user = $em->getRepository(Utilisateur::class)->findOneBy(['mail' => $email]);

            if (!$user) {
                $this->addFlash('success', 'Si cet email existe, un lien de réinitialisation vous sera envoyé');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('success', 'Un lien de réinitialisation vous a été envoyé par email');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig');
    }
}