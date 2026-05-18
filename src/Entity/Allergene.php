<?php

namespace App\Entity;

use App\Repository\AllergeneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllergeneRepository::class)]
#[ORM\Table(name: 'allergene')]
#[ORM\UniqueConstraint(name: 'uk_nom', columns: ['nom'])]
class Allergene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80, unique: true)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $codeEu = null;

    #[ORM\ManyToMany(targetEntity: Plat::class, mappedBy: 'allergenes')]
    private Collection $plats;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
    }

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getCodeEu(): ?string
    {
        return $this->codeEu;
    }

    public function setCodeEu(?string $codeEu): static
    {
        $this->codeEu = $codeEu;
        return $this;
    }

    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->addAllergene($this);
        }
        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->plats->removeElement($plat)) {
            $plat->removeAllergene($this);
        }
        return $this;
    }
}