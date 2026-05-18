<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ResetTokenService;
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
        // Si l'utilisateur est déjà connecté, rediriger vers l'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        // Récupérer les erreurs de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Récupérer le dernier email saisi
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
        // Si l'utilisateur est déjà connecté, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('prenom');
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            
            // Valider
            if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires');
                return $this->redirectToRoute('app_register');
            }
            
            if ($password !== $passwordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_register');
            }
            
            if (strlen($password) < 8) {
                $this->addFlash('error', 'Le mot de passe doit faire au moins 8 caractères');
                return $this->redirectToRoute('app_register');
            }
            
            // Vérifier que l'email n'existe pas déjà
            $existingUser = $em->getRepository(Utilisateur::class)->findOneBy(['mail' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé');
                return $this->redirectToRoute('app_register');
            }
            
            // Créer l'utilisateur
            $user = new Utilisateur();
            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setMail($email);
            $user->setRole('UTILISATEUR'); // Rôle par défaut
            $user->setActif(true);
            
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setMdpHash($hashedPassword);
            
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Compte créé avec succès ! Connectez-vous');
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/register.html.twig');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // Cette méthode peut rester vide
        // La déconnexion est gérée par Symfony
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
            
            // Mettre à jour les infos
            if (!empty($prenom)) $user->setPrenom($prenom);
            if (!empty($nom)) $user->setNom($nom);
            if (!empty($gsm)) $user->setGsm($gsm);
            if (!empty($adresse)) $user->setAdresse($adresse);
            
            // Mettre à jour le mot de passe si fourni
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $this->addFlash('error', 'Le mot de passe doit faire au moins 8 caractères');
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
#[Route('/reset-password', name: 'app_reset_password', methods: ['GET', 'POST'])]
public function resetPassword(
    Request $request,
    EntityManagerInterface $em
): Response
{
    if ($this->getUser()) {
        return $this->redirectToRoute('app_home');
    }

    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');

        if (empty($email)) {
            $this->addFlash('error', 'L\'email est obligatoire');
            return $this->redirectToRoute('app_reset_password');
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['mail' => $email]);

        // Ne pas révéler si l'email existe (sécurité)
        if (!$user) {
            $this->addFlash('success', 'Si cet email existe, un lien de réinitialisation vous sera envoyé');
            return $this->redirectToRoute('app_login');
        }

        // TODO : créer le token et envoyer par email
        $this->addFlash('success', 'Un lien de réinitialisation vous a été envoyé par email');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/reset_password.html.twig');
}

    #[Route('/reset-password/{token}', name: 'app_reset_password_confirm', methods: ['GET', 'POST'])]
    public function resetPasswordConfirm(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ResetTokenService $resetTokenService
    ): Response
    {
        $resetToken = $resetTokenService->getValidToken($token);

        if (!$resetToken) {
            $this->addFlash('error', 'Ce lien de réinitialisation n\'est pas valide ou a expiré');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');

                if (empty($newPassword) || empty($passwordConfirm)) {
                    $this->addFlash('error', 'Tous les champs sont obligatoires');
                    return $this->redirectToRoute('app_reset_password_confirm', ['token' => $token]);
                }

                if ($newPassword !== $passwordConfirm) {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                    return $this->redirectToRoute('app_reset_password_confirm', ['token' => $token]);
                }

            if (strlen($newPassword) < 10) {
                $this->addFlash('error', 'Le mot de passe doit faire au moins 10 caractères');
                return $this->redirectToRoute('app_reset_password_confirm', ['token' => $token]);
            }

            // Mettre à jour le mot de passe
            $user = $resetToken->getUtilisateur();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setMdpHash($hashedPassword);

            $resetTokenService->markTokenAsUsed($resetToken);
            $em->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé ! Connectez-vous');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_confirm.html.twig', [
            'token' => $token,
        ]);
    }


}