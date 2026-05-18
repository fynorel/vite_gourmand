<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail = 'vitegourmandbk@gmail.com'
    ) {
    }

    public function sendWelcomeEmail(Utilisateur $user): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($user->getMail())
            ->subject('Bienvenue chez Vite & Gourmand !')
            ->html($this->renderWelcomeTemplate($user));

        $this->mailer->send($email);
    }

    public function sendResetPasswordEmail(Utilisateur $user, string $resetUrl): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($user->getMail())
            ->subject('Réinitialiser votre mot de passe')
            ->html($this->renderResetPasswordTemplate($user, $resetUrl));

        $this->mailer->send($email);
    }

    private function renderWelcomeTemplate(Utilisateur $user): string
    {
        return <<<EOF
        <h1>Bienvenue chez Vite & Gourmand !</h1>
        <p>Bonjour {$user->getPrenom()} {$user->getNom()},</p>
        <p>Merci de vous être inscrit sur notre plateforme !</p>
        <p>Vous pouvez maintenant découvrir nos menus et passer vos commandes.</p>
        <p><a href="http://localhost:8000/login">Cliquez ici pour vous connecter</a></p>
        <p>Cordialement,<br>L'équipe Vite & Gourmand</p>
        EOF;
    }

    private function renderResetPasswordTemplate(Utilisateur $user, string $resetUrl): string
    {
        return <<<EOF
        <h1>Réinitialiser votre mot de passe</h1>
        <p>Bonjour {$user->getPrenom()},</p>
        <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
        <p><a href="{$resetUrl}">Cliquez ici pour réinitialiser votre mot de passe</a></p>
        <p>Ce lien expire dans 1 heure.</p>
        <p>Si vous n'avez pas demandé cette réinitialisation, ignorez ce message.</p>
        <p>Cordialement,<br>L'équipe Vite & Gourmand</p>
        EOF;
    }
}
