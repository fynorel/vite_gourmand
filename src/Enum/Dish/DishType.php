<?php

namespace App\Enum\Dish;

enum DishType: string
{
    case ENTREE = 'ENTREE';
    case PLAT = 'PLAT';
    case DESSERT = 'DESSERT';
    case AMUSE_BOUCHE = 'AMUSE_BOUCHE';

    public function label(): string
    {
        return match($this) {
            self::ENTREE => 'Entrée',
            self::PLAT => 'Plat principal',
            self::DESSERT => 'Dessert',
            self::AMUSE_BOUCHE => 'Amuse-bouche',
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
