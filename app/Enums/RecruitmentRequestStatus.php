<?php

namespace App\Enums;

enum RecruitmentRequestStatus: string
{
    use HasLabel;

    case Soumise = 'soumise';
    case EnTraitement = 'en_traitement';
    case Pourvue = 'pourvue';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::Soumise => 'Soumise',
            self::EnTraitement => 'En traitement',
            self::Pourvue => 'Pourvue',
            self::Annulee => 'Annulée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Soumise => 'secondary',
            self::EnTraitement => 'info',
            self::Pourvue => 'success',
            self::Annulee => 'danger',
        };
    }
}
