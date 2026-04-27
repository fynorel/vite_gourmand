<?php

namespace App\Enum\Order;

enum OrderContactMode: string
{
    case GSM = 'GSM';
    case MAIL = 'MAIL';

    public function label(): string
    {
        return match($this) {
            self::GSM => 'Par SMS/Téléphone',
            self::MAIL => 'Par Email',
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
