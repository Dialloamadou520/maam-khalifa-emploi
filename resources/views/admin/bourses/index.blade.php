@extends('layouts.espace')

@section('titre', 'Bourses & financements')
@section('sous_titre', 'Opportunités publiées dans l\'espace public et proposées aux candidats.')

@section('actions')
    <a href="{{ route('admin.bourses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Ajouter</a>
@endsection

@section('espace')
    <form method="GET" class="d-flex gap-1 mb-3" style="max-width: 420px">
        <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Titre ou organisme" aria-label="Rechercher">
        <button class="btn btn-sm btn-outline-primary" aria-label="Rechercher"><i class="bi bi-search"></i></button>
    </form>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Bourse</th><th>Type</th><th>Date limite</th><th class="text-center">Dossiers</th><th>Visibilité</th><th></th></tr></thead>
                <tbody>
                    @forelse ($bourses as $bourse)
                        <tr>
                            <td><span class="fw-semibold">{{ $bourse->titre }}</span><div class="small text-muted">{{ $bourse->organisme }} · {{ $bourse->pays }}</div></td>
                            <td class="small">{{ $bourse->type->label() }}</td>
                            <td class="small text-nowrap">
                                {{ $bourse->date_limite?->translatedFormat('d M Y') ?? '—' }}
                                @if ($bourse->date_limite?->isPast())<span class="badge text-bg-secondary">Expirée</span>@endif
                            </td>
                            <td class="text-center">{{ $bourse->service_requests_count }}</td>
                            <td>@if ($bourse->est_publiee)<span class="badge text-bg-success">Publiée</span>@else<span class="badge text-bg-secondary">Masquée</span>@endif</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.bourses.edit', $bourse) }}" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.bourses.destroy', $bourse) }}" class="d-inline" onsubmit="return confirm('Supprimer cette bourse ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucune bourse.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $bourses->links() }}</div>
@endsection
