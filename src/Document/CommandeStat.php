<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use DateTime;

#[MongoDB\Document(collection: 'commandes_stats')]
class CommandeStat
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: 'int')]
    private ?int $menuId = null;

    #[MongoDB\Field(type: 'string')]
    private ?string $menuTitre = null;

    #[MongoDB\Field(type: 'date')]
    private ?DateTime $date = null;

    #[MongoDB\Field(type: 'int')]
    private ?int $nombreCommandes = 0;

    #[MongoDB\Field(type: 'float')]
    private ?float $prixTotal = 0;

    #[MongoDB\Field(type: 'date')]
    private ?DateTime $dateCreation = null;

    // Getters et Setters
    public function getId(): ?string { return $this->id; }
    public function getMenuId(): ?int { return $this->menuId; }
    public function setMenuId(?int $menuId): self { $this->menuId = $menuId; return $this; }
    
    public function getMenuTitre(): ?string { return $this->menuTitre; }
    public function setMenuTitre(?string $menuTitre): self { $this->menuTitre = $menuTitre; return $this; }
    
    public function getDate(): ?DateTime { return $this->date; }
    public function setDate(?DateTime $date): self { $this->date = $date; return $this; }
    
    public function getNombreCommandes(): ?int { return $this->nombreCommandes; }
    public function setNombreCommandes(?int $nombreCommandes): self { $this->nombreCommandes = $nombreCommandes; return $this; }
    
    public function getPrixTotal(): ?float { return $this->prixTotal; }
    public function setPrixTotal(?float $prixTotal): self { $this->prixTotal = $prixTotal; return $this; }
    
    public function getDateCreation(): ?DateTime { return $this->dateCreation; }
    public function setDateCreation(?DateTime $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
}
