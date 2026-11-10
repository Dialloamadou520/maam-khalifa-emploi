<?php

namespace App\Enums;

enum ServiceTarget: string
{
    use HasLabel;

    case Candidat = 'candidat';
    case Recruteur = 'recruteur';
    case Tous = 'tous';

    public function label(): string
    {
        return match ($this) {
            self::Candidat => 'Candidats',
            self::Recruteur => 'Entreprises',
            self::Tous => 'Tous publics',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Candidat => 'primary',
            self::Recruteur => 'info',
            self::Tous => 'secondary',
        };
    }
}
