<?php

namespace App\Enums;

enum DocumentType: string
{
    use HasLabel;

    case Cv = 'cv';
    case LettreMotivation = 'lettre_motivation';
    case Diplome = 'diplome';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Cv => 'CV',
            self::LettreMotivation => 'Lettre de motivation',
            self::Diplome => 'Diplôme / attestation',
            self::Autre => 'Autre document',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Cv => 'primary',
            self::LettreMotivation => 'info',
            self::Diplome => 'success',
            self::Autre => 'secondary',
        };
    }
}
