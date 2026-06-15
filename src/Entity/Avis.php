<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: 'avis')]
#[ORM\UniqueConstraint(name: 'uk_id_commande', columns: ['id_commande'])]
#[ORM\Index(name: 'idx_id_utilisateur', columns: ['id_utilisateur'])]
#[ORM\Index(name: 'idx_statut', columns: ['statut'])]
#[ORM\Index(name: 'idx_validate_par', columns: ['validate_par'])]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_avis')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Commande::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande', nullable: false, onDelete: 'CASCADE')]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false, onDelete: 'RESTRICT')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'integer')]
    private ?int $note = null;

    #[ORM\Column(type: 'text')]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $statut = 'EN_ATTENTE';

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'validate_par', referencedColumnName: 'id_utilisateur', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $validatePar = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(name: 'date_moderation', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateModeration = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        if ($note < 1 || $note > 5) {
            throw new \InvalidArgumentException('La note doit être entre 1 et 5');
        }
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getValidatePar(): ?Utilisateur
    {
        return $this->validatePar;
    }

    public function setValidatePar(?Utilisateur $validatePar): static
    {
        $this->validatePar = $validatePar;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModeration(): ?\DateTimeImmutable
    {
        return $this->dateModeration;
    }

    public function setDateModeration(?\DateTimeImmutable $dateModeration): static
    {
        $this->dateModeration = $dateModeration;
        return $this;
    }

    // ───────────────── MÉTHODES UTILES ─────────────────

    public function isPublished(): bool
    {
        return $this->statut === 'VALIDE';
    }

    public function isPending(): bool
    {
        return $this->statut === 'EN_ATTENTE';
    }

    public function isRefused(): bool
    {
        return $this->statut === 'REFUSE';
    }
}