<?php

namespace App\Entity;

use App\Enum\Review\ReviewStatus;
use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column(enumType: ReviewStatus::class)]
    private ?ReviewStatus $statut = null;

    #[ORM\ManyToOne]
    private ?Utilisateur $validatePar = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column]
    private ?\DateTime $dateModeration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
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

    public function getStatut(): ?ReviewStatus
    {
        return $this->statut;
    }

    public function setStatut(ReviewStatus $statut): static
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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModeration(): ?\DateTime
    {
        return $this->dateModeration;
    }

    public function setDateModeration(\DateTime $dateModeration): static
    {
        $this->dateModeration = $dateModeration;

        return $this;
    }
}
