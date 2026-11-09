<?php

namespace App\Enums;

enum ScholarshipType: string
{
    use HasLabel;

    case Bourse = 'bourse';
    case Financement = 'financement';
    case Formation = 'formation';
    case Subvention = 'subvention';

    public function label(): string
    {
        return match ($this) {
            self::Bourse => 'Bourse d\'études',
            self::Financement => 'Financement de projet',
            self::Formation => 'Formation financée',
            self::Subvention => 'Subvention',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Bourse => 'primary',
            self::Financement => 'success',
            self::Formation => 'info',
            self::Subvention => 'warning',
        };
    }
}
