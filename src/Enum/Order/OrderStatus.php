<?php

namespace App\Enum\Order;

enum OrderStatus: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ACCEPTE = 'ACCEPTE';
    case EN_PREPARATION = 'EN_PREPARATION';
    case EN_COURS_LIVRAISON = 'EN_COURS_LIVRAISON';
    case LIVRE = 'LIVRE';
    case RETOUR_MATERIEL = 'RETOUR_MATERIEL';
    case TERMINEE = 'TERMINEE';
    case ANNULEE = 'ANNULEE';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::ACCEPTE => 'Acceptée',
            self::EN_PREPARATION => 'En préparation',
            self::EN_COURS_LIVRAISON => 'En cours de livraison',
            self::LIVRE => 'Livrée',
            self::RETOUR_MATERIEL => 'Retour matériel',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
        };
    }

    /**
     * Couleur Bootstrap pour affichage
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::ACCEPTE => 'info',
            self::EN_PREPARATION => 'primary',
            self::EN_COURS_LIVRAISON => 'primary',
            self::LIVRE => 'success',
            self::RETOUR_MATERIEL => 'danger',
            self::TERMINEE => 'secondary',
            self::ANNULEE => 'danger',
        };
    }

    /**
     * Vérifications de workflow
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::TERMINEE, self::ANNULEE]);
    }

    public function isActive(): bool
    {
        return !$this->isTerminal();
    }

    /**
     * Transitions autorisées (d'après le MPD §17)
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::EN_ATTENTE => [self::ACCEPTE, self::ANNULEE],
            self::ACCEPTE => [self::EN_PREPARATION, self::ANNULEE],
            self::EN_PREPARATION => [self::EN_COURS_LIVRAISON, self::ANNULEE],
            self::EN_COURS_LIVRAISON => [self::LIVRE, self::ANNULEE],
            self::LIVRE => [self::RETOUR_MATERIEL, self::TERMINEE],
            self::RETOUR_MATERIEL => [self::TERMINEE],
            self::TERMINEE => [],
            self::ANNULEE => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->label(), self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }
}
