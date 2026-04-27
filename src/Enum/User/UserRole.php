<?php

namespace App\Enum\User;

enum UserRole: string
{
    case UTILISATEUR = 'UTILISATEUR';
    case EMPLOYE = 'EMPLOYE';
    case ADMINISTRATEUR = 'ADMINISTRATEUR';

    /**
     * Libellé lisible pour l'interface
     */
    public function label(): string
    {
        return match($this) {
            self::UTILISATEUR => 'Client',
            self::EMPLOYE => 'Employé',
            self::ADMINISTRATEUR => 'Administrateur',
        };
    }

    /**
     * Vérification administrateur
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMINISTRATEUR;
    }

    /**
     * Vérification employé (incluant admin)
     */
    public function isStaff(): bool
    {
        return in_array($this, [self::EMPLOYE, self::ADMINISTRATEUR]);
    }

    /**
     * Liste pour les formulaires
     */
    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->label(), self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }
}
