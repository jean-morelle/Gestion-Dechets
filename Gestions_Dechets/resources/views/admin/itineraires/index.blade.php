@extends('layouts.app')

@section('title', 'Tournées')

@section('content')
<div class="page-header">
    <div>
        <h1>Tournées</h1>
        <p>Planification des collectes et suivi des collecteurs sur le terrain.</p>
    </div>
    <a href="{{ route('admin.itineraires.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i>Planifier une tournée
    </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-sm-6 col-md-3">
        <label for="filtre-statut" class="visually-hidden">Statut</label>
        <select class="form-select" id="filtre-statut" name="statut" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
            <option value="planifie" @selected(request('statut') === 'planifie')>Planifiées</option>
            <option value="termine" @selected(request('statut') === 'termine')>Terminées</option>
        </select>
    </div>
    <div class="col-sm-6 col-md-3">
        <label for="filtre-collecteur" class="visually-hidden">Collecteur</label>
        <select class="form-select" id="filtre-collecteur" name="collecteur_id" onchange="this.form.submit()">
            <option value="">Tous les collecteurs</option>
            @foreach($collecteurs as $c)
                <option value="{{ $c->id }}" @selected((int) request('collecteur_id') === $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <noscript><div class="col-auto"><button class="btn btn-outline-secondary">Filtrer</button></div></noscript>
</form>

<div class="card">
    @if($itineraires->isEmpty())
        <div class="empty-state">
            <i class="fas fa-route" aria-hidden="true"></i>
            @if(request()->hasAny(['statut', 'collecteur_id']))
                <p>Aucune tournée ne correspond à ces critères.</p>
                <a href="{{ route('admin.itineraires.index') }}" class="btn btn-outline-primary btn-sm">Effacer les filtres</a>
            @else
                <p>Aucune tournée planifiée. Une tournée regroupe des points de collecte, dans l’ordre, confiés à un collecteur.</p>
                <a href="{{ route('admin.itineraires.create') }}" class="btn btn-primary btn-sm">Planifier une tournée</a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tournée</th>
                        <th>Collecteur</th>
                        <th>Date</th>
                        <th>Avancement</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itineraires as $itineraire)
                        @php
                            $p = $itineraire->progression;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.itineraires.show', $itineraire) }}" class="fw-medium">{{ $itineraire->nom }}</a>
                                <div class="small text-body-secondary">
                                    {{ $itineraire->points_de_collecte_count }} étape{{ $itineraire->points_de_collecte_count > 1 ? 's' : '' }}
                                    @if($itineraire->incidents_count)
                                        · <span class="text-danger"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i> {{ $itineraire->incidents_count }} incident{{ $itineraire->incidents_count > 1 ? 's' : '' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $itineraire->collecteur->name ?? '—' }}</td>
                            <td class="text-nowrap">
                                {{ $itineraire->date_debut?->translatedFormat('j M Y') }}
                                <div class="small text-body-secondary">{{ $itineraire->heure_debut?->format('H:i') }} – {{ $itineraire->heure_fin?->format('H:i') }}</div>
                            </td>
                            <td style="min-width: 140px">
                                @if($itineraire->statut === 'planifie')
                                    <span class="small text-body-secondary">Pas encore démarrée</span>
                                @else
                                    <div class="progress" style="height: 6px" role="progressbar" aria-label="Avancement" aria-valuenow="{{ $p['pourcentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success" style="width: {{ $p['pourcentage'] }}%"></div>
                                    </div>
                                    <div class="small text-body-secondary mt-1">{{ $p['collectees'] }}/{{ $p['total'] }} collectés @if($p['ratees']) · {{ $p['ratees'] }} non collecté{{ $p['ratees'] > 1 ? 's' : '' }} @endif</div>
                                @endif
                            </td>
                            <td><span class="badge {{ $itineraire->statut_tone }}">{{ $itineraire->statut_label }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $itineraires->links() }}</div>
@endsection
