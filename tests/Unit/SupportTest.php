<?php

namespace Tests\Unit;

use App\Enums\ContractType;
use App\Enums\OfferStatus;
use PHPUnit\Framework\TestCase;

class SupportTest extends TestCase
{
    public function test_fcfa_formate_les_montants(): void
    {
        $this->assertSame('15 000 FCFA', fcfa(15000));
        $this->assertSame('0 FCFA', fcfa(null));
        $this->assertSame('1 250 000 FCFA', fcfa(1250000));
    }

    public function test_les_enums_exposent_leurs_options(): void
    {
        $this->assertSame('CDI', ContractType::options()['cdi']);
        $this->assertContains('en_attente', OfferStatus::values());
        $this->assertSame('warning', OfferStatus::EnAttente->couleur());
    }
}
