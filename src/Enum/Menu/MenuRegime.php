<?php

namespace App\Enum\Menu;

enum MenuRegime: string
{
    case CLASSIQUE = 'CLASSIQUE';
    case VEGETARIEN = 'VEGETARIEN';
    case VEGAN = 'VEGAN';
    case SANS_GLUTEN = 'SANS_GLUTEN';

    public function label(): string
    {
        return match($this) {
            self::CLASSIQUE => 'Classique',
            self::VEGETARIEN => '🥗 Végétarien',
            self::VEGAN => '🌱 Vegan',
            self::SANS_GLUTEN => '🌾 Sans gluten',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CLASSIQUE => '🍽️',
            self::VEGETARIEN => '🥗',
            self::VEGAN => '🌱',
            self::SANS_GLUTEN => '🌾',
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
