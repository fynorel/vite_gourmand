<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
#[ORM\Table(name: 'horaire')]
#[ORM\UniqueConstraint(name: 'uk_entreprise_jour', columns: ['id_entreprise', 'jour_semaine'])]
#[ORM\Index(name: 'idx_id_entreprise', columns: ['id_entreprise'])]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Entreprise::class, inversedBy: 'horaires', cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'id_entreprise', referencedColumnName: 'id_entreprise', nullable: false, onDelete: 'CASCADE')]
    private ?Entreprise $entreprise = null;

    #[ORM\Column(name: 'jour_semaine', type: 'integer')]
    private ?int $jourSemaine = null;

    #[ORM\Column(name: 'heure_ouverture', type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $heureOuverture = null;

    #[ORM\Column(name: 'heure_fermeture', type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $heureFermeture = null;

    #[ORM\Column(name: 'est_ferme', type: 'boolean')]
    private bool $estFerme = false;

    // ───────────────── CONSTANTES ─────────────────

    public const JOURS_SEMAINE = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    // ───────────────── GETTERS / SETTERS ─────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;
        return $this;
    }

    public function getJourSemaine(): ?int
    {
        return $this->jourSemaine;
    }

    public function setJourSemaine(int $jourSemaine): static
    {
        if ($jourSemaine < 1 || $jourSemaine > 7) {
            throw new \InvalidArgumentException('Le jour de la semaine doit être entre 1 (Lundi) et 7 (Dimanche)');
        }
        $this->jourSemaine = $jourSemaine;
        return $this;
    }

    public function getHeureOuverture(): ?\DateTimeImmutable
    {
        return $this->heureOuverture;
    }

    public function setHeureOuverture(?\DateTimeImmutable $heureOuverture): static
    {
        $this->heureOuverture = $heureOuverture;
        return $this;
    }

    public function getHeureFermeture(): ?\DateTimeImmutable
    {
        return $this->heureFermeture;
    }

    public function setHeureFermeture(?\DateTimeImmutable $heureFermeture): static
    {
        $this->heureFermeture = $heureFermeture;
        return $this;
    }

    public function isEstFerme(): bool
    {
        return $this->estFerme;
    }

    public function setEstFerme(bool $estFerme): static
    {
        $this->estFerme = $estFerme;
        return $this;
    }

    // ───────────────── MÉTHODES UTILES ─────────────────

    public function getJourNom(): string
    {
        return self::JOURS_SEMAINE[$this->jourSemaine] ?? 'Inconnu';
    }

    public function isOuvert(): bool
    {
        return !$this->estFerme && $this->heureOuverture !== null && $this->heureFermeture !== null;
    }

    public function getHorairesFormatted(): string
    {
        if ($this->estFerme) {
            return 'Fermé';
        }
        if ($this->heureOuverture && $this->heureFermeture) {
            return $this->heureOuverture->format('H:i') . ' - ' . $this->heureFermeture->format('H:i');
        }
        return 'Horaires non définis';
    }
}