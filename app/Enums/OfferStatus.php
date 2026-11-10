<?php

namespace App\Enums;

enum OfferStatus: string
{
    use HasLabel;

    case Brouillon = 'brouillon';
    case EnAttente = 'en_attente';
    case Publiee = 'publiee';
    case Rejetee = 'rejetee';
    case Cloturee = 'cloturee';

    public function label(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::EnAttente => 'En attente de validation',
            self::Publiee => 'Publiée',
            self::Rejetee => 'Rejetée',
            self::Cloturee => 'Clôturée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Brouillon => 'secondary',
            self::EnAttente => 'warning',
            self::Publiee => 'success',
            self::Rejetee => 'danger',
            self::Cloturee => 'dark',
        };
    }
}
