@extends('layouts.app')

@section('title', 'Points de collecte')

@section('content')
<div class="page-header">
    <div>
        <h1>Points de collecte</h1>
        <p>Bacs, marchés et dépôts par lesquels passent les tournées.</p>
    </div>
    <a href="{{ route('admin.points.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i>Ajouter un point
    </a>
</div>

<form method="GET" class="row g-2 mb-3" role="search">
    <div class="col-md-5">
        <label for="q" class="visually-hidden">Rechercher</label>
        <input type="search" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Nom ou adresse…">
    </div>
    <div class="col-6 col-md-3">
        <label for="filtre-quartier" class="visually-hidden">Quartier</label>
        <select class="form-select" id="filtre-quartier" name="quartier" onchange="this.form.submit()">
            <option value="">Tous les quartiers</option>
            @foreach($quartiers as $q)
                <option value="{{ $q }}" @selected(request('quartier') === $q)>{{ $q }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label for="filtre-statut" class="visually-hidden">Statut</label>
        <select class="form-select" id="filtre-statut" name="statut" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\PointDeCollecte::STATUTS as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
    </div>
</form>

@if($points->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-map-location-dot" aria-hidden="true"></i>
            @if(request()->hasAny(['q', 'quartier', 'statut']))
                <p>Aucun point ne correspond à ces critères.</p>
                <a href="{{ route('admin.points.index') }}" class="btn btn-outline-primary btn-sm">Effacer les filtres</a>
            @else
                <p>Aucun point de collecte pour l’instant. Commencez par les bacs et marchés de la commune : ils serviront à composer les tournées.</p>
                <a href="{{ route('admin.points.create') }}" class="btn btn-primary btn-sm">Ajouter le premier point</a>
            @endif
        </div>
    </div>
@else
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Point</th>
                                <th>Quartier</th>
                                <th>Statut</th>
                                <th class="text-end">Passages</th>
                                <th><span class="visually-hidden">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($points as $point)
                                <tr>
                                    <td class="min-w-0">
                                        <div class="fw-medium">{{ $point->nom }}</div>
                                        <div class="small text-body-secondary">{{ $point->type_label }} · {{ Str::limit($point->adresse, 40) }}</div>
                                    </td>
                                    <td>{{ $point->quartier }}</td>
                                    <td><span class="badge {{ $point->statut_tone }}">{{ $point->statut_label }}</span></td>
                                    <td class="text-end">{{ $point->collectes_count }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.points.edit', $point) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">{{ $points->links() }}</div>
        </div>
        <div class="col-xl-5">
            <x-carte.points class="position-sticky" style="top: calc(var(--cp-topbar-height) + 1rem)" hauteur="480px"
                :points="$pointsCarte->map(fn ($p) => [
                    'lat' => $p->latitude, 'lng' => $p->longitude, 'nom' => $p->nom,
                    'url' => route('admin.points.edit', $p),
                    'etat' => $p->statut === 'actif' ? 'fait' : 'inactif',
                ])" />
        </div>
    </div>
@endif
@endsection
