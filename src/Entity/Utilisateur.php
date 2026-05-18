<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[ORM\UniqueConstraint(name: 'uk_mail', columns: ['mail'])]
#[ORM\Index(name: 'idx_role', columns: ['role'])]
#[ORM\Index(name: 'idx_actif', columns: ['actif'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 80)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $mail = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $gsm = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $mdp_hash = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $role = 'UTILISATEUR';

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'compteur_authentification', type: 'integer')]
    private int $compteur_authentification = 0;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    // Relations
    /** @var Collection<int, Commande> */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'utilisateur')]
    private Collection $commandes;

    /** @var Collection<int, Session> */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'utilisateur', cascade: ['remove'])]
    private Collection $sessions;

    /** @var Collection<int, ResetToken> */
    #[ORM\OneToMany(targetEntity: ResetToken::class, mappedBy: 'utilisateur', cascade: ['remove'])]
    private Collection $resetTokens;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->commandes = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->resetTokens = new ArrayCollection();
    }

    // ───────────────── UserInterface Implementation ─────────────────

    public function getUserIdentifier(): string
    {
        return $this->mail ?? '';
    }

    public function getRoles(): array
    {
       
        return [$this->role];
    }

    public function getPassword(): ?string
    {
        return $this->mdp_hash;
    }

    public function eraseCredentials(): void
    {
        // N/A
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): static
    {
        $this->gsm = $gsm;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getMdpHash(): ?string
    {
        return $this->mdp_hash;
    }

    public function setMdpHash(string $mdp_hash): static
    {
        $this->mdp_hash = $mdp_hash;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }


   
    public function setRole(string $role): static
    {
        $this->role = $role;
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

    public function getCompteurAuthentification(): int
    {
        return $this->compteur_authentification;
    }

    public function setCompteurAuthentification(int $compteur): static
    {
        $this->compteur_authentification = $compteur;
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

    // ───────────────── Relations Collections ─────────────────

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
            $commande->setUtilisateur($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUtilisateur($this);
        }
        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getUtilisateur() === $this) {
                $session->setUtilisateur(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ResetToken>
     */
    public function getResetTokens(): Collection
    {
        return $this->resetTokens;
    }

    public function addResetToken(ResetToken $resetToken): static
    {
        if (!$this->resetTokens->contains($resetToken)) {
            $this->resetTokens->add($resetToken);
            $resetToken->setUtilisateur($this);
        }
        return $this;
    }

    public function removeResetToken(ResetToken $resetToken): static
    {
        if ($this->resetTokens->removeElement($resetToken)) {
            if ($resetToken->getUtilisateur() === $this) {
                $resetToken->setUtilisateur(null);
            }
        }
        return $this;
    }
}