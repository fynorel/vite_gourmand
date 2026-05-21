<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
#[ORM\Index(name: 'idx_id_util', columns: ['id_utilisateur'])]
#[ORM\Index(name: 'idx_id_menu', columns: ['id_menu'])]
#[ORM\Index(name: 'idx_statut', columns: ['statut'])]
#[ORM\Index(name: 'idx_date_presta', columns: ['date_prestation'])]
#[ORM\Index(name: 'idx_annule_par', columns: ['annule_par'])]
#[ORM\Index(name: 'idx_util_statut', columns: ['id_utilisateur', 'statut'])]
#[ORM\Index(name: 'idx_menu_statut', columns: ['id_menu', 'statut'])]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(name: 'id_menu', referencedColumnName: 'id_menu', nullable: false, onDelete: 'RESTRICT')]
    private ?Menu $menu = null;

    #[ORM\Column(type: 'integer')]
    private ?int $nbPersonnes = null;

    #[ORM\Column(type: 'text')]
    private ?string $adresse = null;

    #[ORM\Column(name: 'date_prestation', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $datePrestation = null;

    #[ORM\Column(name: 'prix_menu', type: 'decimal', precision: 8, scale: 2)]
    private ?string $prixMenu = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private string $reduction = '0.00';

    #[ORM\Column(name: 'frais_livraison', type: 'decimal', precision: 6, scale: 2)]
    private string $fraisLivraison = '0.00';

    #[ORM\Column(name: 'prix_total', type: 'decimal', precision: 8, scale: 2)]
    private ?string $prixTotal = null;

    #[ORM\Column(type: 'string', length: 30)]
    private string $statut = 'EN_ATTENTE';

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(name: 'mode_contact_annul', type: 'string', length: 10, nullable: true)]
    private ?string $modeContactAnnul = null;

    #[ORM\Column(name: 'motif_annulation', type: 'text', nullable: true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(name: 'date_contact_annul', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateContactAnnul = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'annule_par', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $annulePar = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $materielPrete = false;

    #[ORM\Column(name: 'date_retour_attendue', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateRetourAttendueDate = null;

    // ───────────────── RELATIONS ─────────────────

    /** @var Collection<int, HistoriqueStatut> */
    #[ORM\OneToMany(targetEntity: HistoriqueStatut::class, mappedBy: 'commande', cascade: ['remove'])]
    private Collection $historiques;

    #[ORM\OneToOne(targetEntity: Avis::class, mappedBy: 'commande', cascade: ['remove'])]
    private ?Avis $avis = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->historiques = new ArrayCollection();
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

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;
        return $this;
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

    public function getDatePrestation(): ?\DateTimeImmutable
    {
        return $this->datePrestation;
    }

    public function setDatePrestation(\DateTimeImmutable $datePrestation): static
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

    public function getReduction(): string
    {
        return $this->reduction;
    }

    public function setReduction(string $reduction): static
    {
        $this->reduction = $reduction;
        return $this;
    }

    public function getFraisLivraison(): string
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

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
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

    public function getModeContactAnnul(): ?string
    {
        return $this->modeContactAnnul;
    }

    public function setModeContactAnnul(?string $modeContactAnnul): static
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

    public function getDateContactAnnul(): ?\DateTimeImmutable
    {
        return $this->dateContactAnnul;
    }

    public function setDateContactAnnul(?\DateTimeImmutable $dateContactAnnul): static
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

    // ───────────────── RELATIONS (Collections) ─────────────────

    /**
     * @return Collection<int, HistoriqueStatut>
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(HistoriqueStatut $historique): static
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques->add($historique);
            $historique->setCommande($this);
        }
        return $this;
    }

    public function removeHistorique(HistoriqueStatut $historique): static
    {
        if ($this->historiques->removeElement($historique)) {
            if ($historique->getCommande() === $this) {
                $historique->setCommande(null);
            }
        }
        return $this;
    }

    public function getAvis(): ?Avis
    {
        return $this->avis;
    }

    public function setAvis(?Avis $avis): static
    {
        $this->avis = $avis;
        return $this;
    }


    public function isMaterielPrete(): ?bool
    {
        return $this->materielPrete;
    }

    public function setMaterielPrete(bool $materielPrete): static
    {
        $this->materielPrete = $materielPrete;
        return $this;
    }

    public function getDateRetourAttendueDate(): ?\DateTimeInterface
    {
        return $this->dateRetourAttendueDate;
    }

    public function setDateRetourAttendueDate(?\DateTimeInterface $dateRetourAttendueDate): static
    {
        $this->dateRetourAttendueDate = $dateRetourAttendueDate;
        return $this;
    }
}