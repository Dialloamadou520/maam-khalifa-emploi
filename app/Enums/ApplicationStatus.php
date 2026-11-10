<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    use HasLabel;

    case Envoyee = 'envoyee';
    case EnExamen = 'en_examen';
    case Preselectionnee = 'preselectionnee';
    case Entretien = 'entretien';
    case Retenue = 'retenue';
    case Refusee = 'refusee';

    public function label(): string
    {
        return match ($this) {
            self::Envoyee => 'Envoyée',
            self::EnExamen => 'En cours d\'examen',
            self::Preselectionnee => 'Présélectionnée',
            self::Entretien => 'Entretien programmé',
            self::Retenue => 'Retenue',
            self::Refusee => 'Non retenue',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Envoyee => 'secondary',
            self::EnExamen => 'info',
            self::Preselectionnee => 'primary',
            self::Entretien => 'warning',
            self::Retenue => 'success',
            self::Refusee => 'danger',
        };
    }
}
