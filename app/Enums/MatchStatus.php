<?php

namespace App\Enums;

enum MatchStatus: string
{
    use HasLabel;

    case Proposee = 'proposee';
    case Acceptee = 'acceptee';
    case Refusee = 'refusee';
    case Embauche = 'embauche';

    public function label(): string
    {
        return match ($this) {
            self::Proposee => 'Proposée',
            self::Acceptee => 'Acceptée par l\'entreprise',
            self::Refusee => 'Refusée',
            self::Embauche => 'Embauche confirmée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Proposee => 'secondary',
            self::Acceptee => 'info',
            self::Refusee => 'danger',
            self::Embauche => 'success',
        };
    }
}
