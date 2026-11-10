<?php

namespace App\Http\Controllers\Recruteur\Concerns;

use App\Models\Company;
use Illuminate\Http\Request;

trait AvecEntreprise
{
    protected function entreprise(Request $request): Company
    {
        return $request->user()->company()->firstOrCreate([], ['nom' => $request->user()->name]);
    }

    protected function verifierEntreprise(Request $request, int|string|null $companyId): void
    {
        abort_unless((int) $companyId === $this->entreprise($request)->id, 403);
    }
}
