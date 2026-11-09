@extends('layouts.espace')

@section('titre', 'Prestations & tarifs')
@section('sous_titre', 'Un changement de tarif s\'applique aux nouvelles demandes uniquement.')

@section('actions')
    <a href="{{ route('admin.prestations.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvelle prestation</a>
@endsection

@section('espace')
    @foreach (\App\Enums\ServiceTarget::cases() as $cible)
        @continue(! isset($prestations[$cible->value]))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">{{ $cible->label() }}</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small"><tr><th>Prestation</th><th>Tarif</th><th>Délai</th><th class="text-center">Demandes</th><th>État</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($prestations[$cible->value] as $p)
                            <tr @class(['text-muted' => ! $p->est_active])>
                                <td><span class="fw-semibold">{{ $p->nom }}</span><div class="small text-muted">{{ \Illuminate\Support\Str::limit($p->description, 90) }}</div></td>
                                <td class="text-nowrap fw-semibold">{{ fcfa($p->prix) }}</td>
                                <td class="small">{{ $p->delai ?? '—' }}</td>
                                <td class="text-center">{{ $p->requests_count }}</td>
                                <td>@if ($p->est_active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Inactive</span>@endif</td>
                                <td class="text-end"><a href="{{ route('admin.prestations.edit', $p) }}" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
