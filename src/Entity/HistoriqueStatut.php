<?php

namespace App\Entity;

use App\Enum\Order\OrderStatus;
use App\Repository\HistoriqueStatutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueStatutRepository::class)]
class HistoriqueStatut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private ?OrderStatus $statut = null;

    #[ORM\Column]
    private ?\DateTime $changedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $changedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?OrderStatus
    {
        return $this->statut;
    }

    public function setStatut(OrderStatus $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getChangedAt(): ?\DateTime
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTime $changedAt): static
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
