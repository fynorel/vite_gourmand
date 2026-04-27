<?php

namespace App\Enum\Review;

enum ReviewStatus: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case VALIDE = 'VALIDE';
    case REFUSE = 'REFUSE';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente de modération',
            self::VALIDE => 'Publié',
            self::REFUSE => 'Refusé',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::VALIDE => 'success',
            self::REFUSE => 'danger',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::VALIDE;
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->label(), self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }
}
