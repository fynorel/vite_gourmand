<?php

namespace App\Entity;

use App\Repository\HistoriqueStatutRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueStatutRepository::class)]
#[ORM\Table(name: 'historique_statut')]
#[ORM\Index(name: 'idx_id_commande', columns: ['id_commande'])]
#[ORM\Index(name: 'idx_changed_at', columns: ['changed_at'])]
#[ORM\Index(name: 'idx_changed_by', columns: ['changed_by'])]
class HistoriqueStatut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_historique')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'historiques')]
    #[ORM\JoinColumn(name: 'id_commande', nullable: false, onDelete: 'CASCADE')]
    private ?Commande $commande = null;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $statut = null;

    #[ORM\Column(name: 'changed_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $changedAt = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'changed_by', nullable: false, onDelete: 'RESTRICT')]
    private ?Utilisateur $changedBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    public function __construct()
    {
        $this->changedAt = new \DateTimeImmutable();
    }

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;
        return $this;
    }

    public function getChangedBy(): ?Utilisateur
    {
        return $this->changedBy;
    }

    public function setChangedBy(?Utilisateur $changedBy): static
    {
        $this->changedBy = $changedBy;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }
}