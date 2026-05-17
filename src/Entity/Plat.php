<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
#[ORM\Table(name: 'plat')]
#[ORM\Index(name: 'idx_type', columns: ['type'])]
#[ORM\Index(name: 'idx_actif', columns: ['actif'])]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_plat')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 120)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $type = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    // ───────────────── RELATIONS ─────────────────

    /** @var Collection<int, Menu> */
    #[ORM\ManyToMany(targetEntity: Menu::class, inversedBy: 'plats')]
    #[ORM\JoinTable(name: 'menu_plat')]
    #[ORM\JoinColumn(name: 'id_plat', referencedColumnName: 'id_plat', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'id_menu', referencedColumnName: 'id_menu', onDelete: 'CASCADE')]
    private Collection $menus;

    /** @var Collection<int, Allergene> */
    #[ORM\ManyToMany(targetEntity: Allergene::class)]
    #[ORM\JoinTable(name: 'plat_allergene')]
    #[ORM\JoinColumn(name: 'id_plat', referencedColumnName: 'id_allergene', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'id_allergene', referencedColumnName: 'id_allergene', onDelete: 'RESTRICT')]
    private Collection $allergenes;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->allergenes = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    // ───────────────── RELATIONS (Collections) ─────────────────

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
        }
        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        $this->menus->removeElement($menu);
        return $this;
    }

    /**
     * @return Collection<int, Allergene>
     */
    public function getAllergenes(): Collection
    {
        return $this->allergenes;
    }

    public function addAllergene(Allergene $allergene): static
    {
        if (!$this->allergenes->contains($allergene)) {
            $this->allergenes->add($allergene);
        }
        return $this;
    }

    public function removeAllergene(Allergene $allergene): static
    {
        $this->allergenes->removeElement($allergene);
        return $this;
    }
}