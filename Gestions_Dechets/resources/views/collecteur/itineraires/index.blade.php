@extends('layouts.app')

@section('title', 'Mes tournées')

@section('content')
<div class="page-header">
    <div>
        <h1>Mes tournées</h1>
        <p>Les tournées que l’administration vous a confiées.</p>
    </div>
</div>

<ul class="nav nav-pills mb-3 small">
    @foreach(['' => 'À faire', 'termine' => 'Terminées'] as $valeur => $libelle)
        <li class="nav-item">
            <a class="nav-link {{ request('statut', '') === $valeur ? 'active' : '' }}" href="{{ route('collecteur.itineraires.index', array_filter(['statut' => $valeur])) }}">{{ $libelle }}</a>
        </li>
    @endforeach
</ul>

@if($itineraires->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-route" aria-hidden="true"></i>
            <p>{{ request('statut') ? 'Aucune tournée terminée pour l’instant.' : 'Aucune tournée à faire. Les nouvelles tournées apparaîtront ici dès que l’administration vous les aura confiées.' }}</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($itineraires as $itineraire)
            @php
                $p = $itineraire->progression;
            @endphp
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('collecteur.itineraires.show', $itineraire) }}" class="card h-100 text-decoration-none text-body tournee-carte {{ $itineraire->statut === 'en_cours' ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h2 class="fs-6 fw-semibold mb-0">{{ $itineraire->nom }}</h2>
                            <span class="badge {{ $itineraire->statut_tone }}">{{ $itineraire->statut_label }}</span>
                        </div>
                        <div class="small text-body-secondary">
                            <i class="far fa-calendar me-1" aria-hidden="true"></i>{{ $itineraire->date_debut?->translatedFormat('l j F') }},
                            {{ $itineraire->heure_debut?->format('H:i') }} – {{ $itineraire->heure_fin?->format('H:i') }}
                        </div>
                        <div class="small text-body-secondary">
                            <i class="fas fa-location-dot me-1" aria-hidden="true"></i>{{ $p['total'] }} étape{{ $p['total'] > 1 ? 's' : '' }}
                            @if($itineraire->distance_estimee) · {{ str_replace('.', ',', (string) (float) $itineraire->distance_estimee) }} km @endif
                        </div>
                        @if($itineraire->statut !== 'planifie')
                            <div class="progress mt-3" style="height: 6px" role="progressbar" aria-label="Avancement" aria-valuenow="{{ $p['pourcentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-success" style="width: {{ $p['pourcentage'] }}%"></div>
                            </div>
                            <div class="small text-body-secondary mt-1">{{ $p['total'] - $p['restantes'] }}/{{ $p['total'] }} étapes faites</div>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $itineraires->links() }}</div>
@endif
@endsection
