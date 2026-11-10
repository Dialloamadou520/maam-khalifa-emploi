<?php

namespace App\Enums;

enum Role: string
{
    use HasLabel;

    case Candidat = 'candidat';
    case Recruteur = 'recruteur';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Candidat => 'Candidat',
            self::Recruteur => 'Recruteur',
            self::Admin => 'Administrateur',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Candidat => 'primary',
            self::Recruteur => 'info',
            self::Admin => 'dark',
        };
    }
}
