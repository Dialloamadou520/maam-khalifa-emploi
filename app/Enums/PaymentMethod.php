<?php

namespace App\Enums;

enum PaymentMethod: string
{
    use HasLabel;

    case Wave = 'wave';
    case OrangeMoney = 'orange_money';
    case FreeMoney = 'free_money';
    case Especes = 'especes';
    case Virement = 'virement';

    public function label(): string
    {
        return match ($this) {
            self::Wave => 'Wave',
            self::OrangeMoney => 'Orange Money',
            self::FreeMoney => 'Free Money',
            self::Especes => 'Espèces (à l\'agence)',
            self::Virement => 'Virement bancaire',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Wave => 'info',
            self::OrangeMoney => 'warning',
            self::FreeMoney => 'danger',
            self::Especes => 'success',
            self::Virement => 'secondary',
        };
    }
}
