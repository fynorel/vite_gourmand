<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $prenom = null;

    #[ORM\Column(length: 80)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $gsm = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp_hash = null;

    #[ORM\Column(enumType: UserRole::class)]
    private ?UserRole $role = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\Column]
    private ?int $compteurAuthentification = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    private Collection $sessions;

    /**
     * @var Collection<int, ResetToken>
     */
    #[ORM\OneToMany(targetEntity: ResetToken::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    private Collection $resetTokens;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->resetTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCompteurAuthentification(): ?int
    {
        return $this->compteurAuthentification;
    }

    public function setCompteurAuthentification(int $compteurAuthentification): static
    {
        $this->compteurAuthentification = $compteurAuthentification;

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
            // set the owning side to null (unless already changed)
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
            // set the owning side to null (unless already changed)
            if ($resetToken->getUtilisateur() === $this) {
                $resetToken->setUtilisateur(null);
            }
        }

        return $this;
    }
}
