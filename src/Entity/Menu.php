<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menu')]
#[ORM\Index(name: 'idx_actif', columns: ['actif'])]
#[ORM\Index(name: 'idx_theme', columns: ['theme'])]
#[ORM\Index(name: 'idx_regime', columns: ['regime'])]
#[ORM\Index(name: 'idx_prix', columns: ['prix'])]
#[ORM\Index(name: 'idx_actif_theme', columns: ['actif', 'theme'])]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_menu')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $theme = 'CLASSIQUE';

    #[ORM\Column(type: 'string', length: 20)]
    private string $regime = 'CLASSIQUE';

    #[ORM\Column(type: 'integer')]
    private ?int $nbPersonnesMin = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: 'integer')]
    private int $stock = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $conditions = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    // ───────────────── RELATIONS ─────────────────

    /** @var Collection<int, ImageMenu> */
    #[ORM\OneToMany(targetEntity: ImageMenu::class, mappedBy: 'menu', cascade: ['remove'])]
    private Collection $images;

    /** @var Collection<int, Plat> */
    #[ORM\ManyToMany(targetEntity: Plat::class, inversedBy: 'menus')]
    #[ORM\JoinTable(name: 'menu_plat')]
    #[ORM\JoinColumn(name: 'id_menu', referencedColumnName: 'id_menu', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'id_plat', referencedColumnName: 'id_plat', onDelete: 'CASCADE')]
    private Collection $plats;
    
    /** @var Collection<int, Commande> */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'menu')]
    private Collection $commandes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->plats = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
    }

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
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

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function getRegime(): string
    {
        return $this->regime;
    }

    public function setRegime(string $regime): static
    {
        $this->regime = $regime;
        return $this;
    }

    public function getNbPersonnesMin(): ?int
    {
        return $this->nbPersonnesMin;
    }

    public function setNbPersonnesMin(int $nbPersonnesMin): static
    {
        $this->nbPersonnesMin = $nbPersonnesMin;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(?string $conditions): static
    {
        $this->conditions = $conditions;
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

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    // ───────────────── RELATIONS (Collections) ─────────────────

    /**
     * @return Collection<int, ImageMenu>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ImageMenu $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setMenu($this);
        }
        return $this;
    }

    public function removeImage(ImageMenu $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getMenu() === $this) {
                $image->setMenu(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
        }
        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        $this->plats->removeElement($plat);
        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setMenu($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getMenu() === $this) {
                $commande->setMenu(null);
            }
        }
        return $this;
    }
}