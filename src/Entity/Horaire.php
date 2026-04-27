<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 7,
        notInRangeMessage: 'Le jour doit être entre 1 (lundi) et 7 (dimanche).'
    )]
    private int $jourSemaine;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $heureOuverture = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $heureFermeture = null;

    #[ORM\Column]
    private bool $estFerme = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJourSemaine(): int
    {
        return $this->jourSemaine;
    }

    public function setJourSemaine(int $jourSemaine): static
    {
        $this->jourSemaine = $jourSemaine;
        return $this;
    }

    public function getJourLabel(): string
    {
        return match($this->jourSemaine) {
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
            default => 'Invalide',
        };
    }

    public function getHeureOuverture(): ?\DateTime
    {
        return $this->heureOuverture;
    }

    public function setHeureOuverture(?\DateTime $heureOuverture): static
    {
        $this->heureOuverture = $heureOuverture;
        return $this;
    }

    public function getHeureFermeture(): ?\DateTime
    {
        return $this->heureFermeture;
    }

    public function setHeureFermeture(?\DateTime $heureFermeture): static
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
}