<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    use HasLabel;

    case Soumis = 'soumis';
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case Annule = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::Soumis => 'Soumis',
            self::EnCours => 'En cours de traitement',
            self::Termine => 'Terminé',
            self::Annule => 'Annulé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Soumis => 'secondary',
            self::EnCours => 'info',
            self::Termine => 'success',
            self::Annule => 'danger',
        };
    }
}
