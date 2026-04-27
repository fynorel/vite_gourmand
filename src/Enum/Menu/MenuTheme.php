<?php

namespace App\Enum\Menu;

enum MenuTheme: string
{
    case NOEL = 'NOEL';
    case PAQUES = 'PAQUES';
    case CLASSIQUE = 'CLASSIQUE';
    case EVENEMENT = 'EVENEMENT';
    case MARIAGE = 'MARIAGE';
    case ENTREPRISE = 'ENTREPRISE';

    public function label(): string
    {
        return match($this) {
            self::NOEL => '🎄 Noël',
            self::PAQUES => '🐰 Pâques',
            self::CLASSIQUE => 'Classique',
            self::EVENEMENT => 'Événement',
            self::MARIAGE => '💍 Mariage',
            self::ENTREPRISE => 'Entreprise',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::NOEL => 'Menu festif de Noël',
            self::PAQUES => 'Menu printanier de Pâques',
            self::CLASSIQUE => 'Menu traditionnel',
            self::EVENEMENT => 'Menu personnalisable',
            self::MARIAGE => 'Menu prestige mariage',
            self::ENTREPRISE => 'Menu séminaire entreprise',
        };
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->label(), self::cases()),
            array_map(fn($case) => $case->value, self::cases())
        );
    }
}
