<?php

namespace App\Entity;

use App\Repository\ResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResetTokenRepository::class)]
#[ORM\Table(name: 'reset_token')]
#[ORM\Index(name: 'idx_id_util', columns: ['id_utilisateur'])]
#[ORM\UniqueConstraint(name: 'uk_token', columns: ['token'])]
class ResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'resetTokens')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $expiry = null;

    #[ORM\Column(type: 'boolean')]
    private bool $used = false;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function generateToken(): void
    {
        if ($this->token === null) {
            $this->token = bin2hex(random_bytes(32));
        }
        if ($this->expiry === null) {
            $this->expiry = new \DateTimeImmutable('+1 hour');
        }
    }

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getExpiry(): ?\DateTimeImmutable
    {
        return $this->expiry;
    }

    public function setExpiry(\DateTimeImmutable $expiry): static
    {
        $this->expiry = $expiry;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Méthode utile
    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiry;
    }
}