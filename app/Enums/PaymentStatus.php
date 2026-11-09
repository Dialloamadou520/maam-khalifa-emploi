<?php

namespace App\Enums;

enum PaymentStatus: string
{
    use HasLabel;

    case EnAttente = 'en_attente';
    case Payee = 'payee';
    case Echouee = 'echouee';
    case Remboursee = 'remboursee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Payee => 'Payé',
            self::Echouee => 'Échoué',
            self::Remboursee => 'Remboursé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::Payee => 'success',
            self::Echouee => 'danger',
            self::Remboursee => 'secondary',
        };
    }
}
