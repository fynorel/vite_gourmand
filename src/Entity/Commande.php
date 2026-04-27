<?php

namespace App\Entity;

use App\Enum\Order\OrderContactMode;
use App\Enum\Order\OrderStatus;
use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbPersonnes = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?\DateTime $datePrestation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $prixMenu = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $reduction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $fraisLivraison = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $prixTotal = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private ?OrderStatus $statut = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(enumType: OrderContactMode::class)]
    private ?OrderContactMode $modeContactAnnul = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateContactAnnul = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Utilisateur $annulePar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbPersonnes(): ?int
    {
        return $this->nbPersonnes;
    }

    public function setNbPersonnes(int $nbPersonnes): static
    {
        $this->nbPersonnes = $nbPersonnes;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDatePrestation(): ?\DateTime
    {
        return $this->datePrestation;
    }

    public function setDatePrestation(\DateTime $datePrestation): static
    {
        $this->datePrestation = $datePrestation;

        return $this;
    }

    public function getPrixMenu(): ?string
    {
        return $this->prixMenu;
    }

    public function setPrixMenu(string $prixMenu): static
    {
        $this->prixMenu = $prixMenu;

        return $this;
    }

    public function getReduction(): ?string
    {
        return $this->reduction;
    }

    public function setReduction(string $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getFraisLivraison(): ?string
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(string $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;

        return $this;
    }

    public function getPrixTotal(): ?string
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(string $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getModeContactAnnul(): ?OrderContactMode
    {
        return $this->modeContactAnnul;
    }

    public function setModeContactAnnul(OrderContactMode $modeContactAnnul): static
    {
        $this->modeContactAnnul = $modeContactAnnul;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getDateContactAnnul(): ?\DateTime
    {
        return $this->dateContactAnnul;
    }

    public function setDateContactAnnul(?\DateTime $dateContactAnnul): static
    {
        $this->dateContactAnnul = $dateContactAnnul;

        return $this;
    }

    public function getAnnulePar(): ?Utilisateur
    {
        return $this->annulePar;
    }

    public function setAnnulePar(?Utilisateur $annulePar): static
    {
        $this->annulePar = $annulePar;

        return $this;
    }
}
