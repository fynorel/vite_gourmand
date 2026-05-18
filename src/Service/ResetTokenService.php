<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Entity\ResetToken;
use Doctrine\ORM\EntityManagerInterface;

class ResetTokenService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createResetToken(Utilisateur $user): ResetToken
    {
        $oldTokens = $this->em->getRepository(ResetToken::class)->findBy(['utilisateur' => $user]);
        foreach ($oldTokens as $token) {
            $this->em->remove($token);
        }
        $this->em->flush();

        $resetToken = new ResetToken();
        $resetToken->setUtilisateur($user);
        $resetToken->setToken(bin2hex(random_bytes(32)));
        $resetToken->setExpiry(new \DateTimeImmutable('+1 hour'));

        $this->em->persist($resetToken);
        $this->em->flush();

        return $resetToken;
    }

    public function getValidToken(string $token): ?ResetToken
    {
        $resetToken = $this->em->getRepository(ResetToken::class)->findOneBy(['token' => $token]);

        if (!$resetToken) {
            return null;
        }

        if ($resetToken->isExpired()) {
            $this->em->remove($resetToken);
            $this->em->flush();
            return null;
        }

        if ($resetToken->isUsed()) {
            return null;
        }

        return $resetToken;
    }

    public function markTokenAsUsed(ResetToken $resetToken): void
    {
        $resetToken->setUsed(true);
        $this->em->flush();
    }
}
