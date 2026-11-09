<?php

namespace App\Enums;

enum ContractType: string
{
    use HasLabel;

    case Cdi = 'cdi';
    case Cdd = 'cdd';
    case Stage = 'stage';
    case Alternance = 'alternance';
    case Interim = 'interim';
    case Freelance = 'freelance';

    public function label(): string
    {
        return match ($this) {
            self::Cdi => 'CDI',
            self::Cdd => 'CDD',
            self::Stage => 'Stage',
            self::Alternance => 'Alternance',
            self::Interim => 'Intérim',
            self::Freelance => 'Freelance / Consultant',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Cdi => 'primary',
            self::Cdd => 'info',
            self::Stage => 'success',
            self::Alternance => 'success',
            self::Interim => 'warning',
            self::Freelance => 'secondary',
        };
    }
}
