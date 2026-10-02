@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    $s = $statistiques;
    $prochaine = $itinerairesRecents->first();
@endphp

<div class="page-header">
    <div>
        <h1>Bonjour, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</h1>
        <p>{{ ucfirst(now()->translatedFormat('l j F Y')) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('collecteur.incidents.create') }}" class="btn btn-outline-danger">
            <i class="fas fa-triangle-exclamation me-1" aria-hidden="true"></i> Signaler un incident
        </a>
        <a href="{{ route('collecteur.itineraires.index') }}" class="btn btn-primary">
            <i class="fas fa-route me-1" aria-hidden="true"></i> Mes itinéraires
        </a>
    </div>
</div>

@if($prochaine)
    <div class="card mb-4 border-primary-subtle">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="stat-icon tone-green"><i class="fas fa-route" aria-hidden="true"></i></span>
            <div class="flex-grow-1 min-w-0">
                <div class="small text-body-secondary">{{ $prochaine->statut === 'en_cours' ? 'Tournée en cours' : 'Prochaine tournée' }}</div>
                <div class="fw-semibold fs-5 text-truncate">{{ $prochaine->nom }}</div>
                <div class="small text-body-secondary">
                    @if($prochaine->date_debut){{ ucfirst($prochaine->date_debut->translatedFormat('l j F')) }}@endif
                    @if($prochaine->heure_debut) · {{ $prochaine->heure_debut->format('H:i') }}@endif
                    @if($prochaine->distance_estimee) · {{ rtrim(rtrim($prochaine->distance_estimee, '0'), '.') }} km @endif
                </div>
            </div>
            <a href="{{ route('collecteur.itineraires.show', $prochaine) }}" class="btn btn-primary">
                {{ $prochaine->statut === 'en_cours' ? 'Continuer' : 'Voir le détail' }}
                <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('collecteur.itineraires.index') }}" class="card stat-card">
            <span class="stat-icon tone-green"><i class="fas fa-route" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['itineraires']['total'] }}</span>
                <span class="stat-label">Tournées · {{ $s['itineraires']['termines'] }} terminée{{ $s['itineraires']['termines'] > 1 ? 's' : '' }}</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('collecteur.collectes.index') }}" class="card stat-card">
            <span class="stat-icon tone-blue"><i class="fas fa-dumpster" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['collectes']['terminees'] }}</span>
                <span class="stat-label">Collectes réalisées</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('collecteur.itineraires.index') }}" class="card stat-card">
            <span class="stat-icon tone-amber"><i class="fas fa-spinner" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['collectes']['en_cours'] }}</span>
                <span class="stat-label">Étapes à faire</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('collecteur.collectes.index', ['statut' => 'rate']) }}" class="card stat-card">
            <span class="stat-icon tone-red"><i class="fas fa-xmark" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['collectes']['ratees'] }}</span>
                <span class="stat-label">Points non collectés</span>
            </span>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Tournées à venir</h5>
                <a href="{{ route('collecteur.itineraires.index') }}" class="small text-decoration-none">Tout voir</a>
            </div>
            @if($itinerairesRecents->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-route d-block" aria-hidden="true"></i>
                    <p class="mb-0">Aucune tournée planifiée. L'administration vous en attribuera une prochainement.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($itinerairesRecents as $itineraire)
                        <li>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('collecteur.itineraires.show', $itineraire) }}" class="d-block text-truncate">{{ $itineraire->nom }}</a>
                                <div class="activity-meta">
                                    {{ $itineraire->date_debut ? ucfirst($itineraire->date_debut->translatedFormat('D j M')) : 'Date à définir' }}
                                    @if($itineraire->heure_debut) · {{ $itineraire->heure_debut->format('H:i') }}@endif
                                </div>
                            </div>
                            <span class="badge {{ $itineraire->statut_tone }}">{{ $itineraire->statut_label }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Dernières collectes</h5>
                <a href="{{ route('collecteur.collectes.index') }}" class="small text-decoration-none">Tout voir</a>
            </div>
            @if($collectesRecentes->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-dumpster d-block" aria-hidden="true"></i>
                    <p class="mb-0">Les collectes apparaîtront ici dès que vous démarrerez une tournée.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($collectesRecentes as $collecte)
                        <li>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('collecteur.collectes.show', $collecte) }}" class="d-block text-truncate">{{ $collecte->pointDeCollecte->nom ?? 'Point de collecte' }}</a>
                                <div class="activity-meta text-truncate">
                                    {{ $collecte->pointDeCollecte->adresse ?? '' }}
                                    · {{ $collecte->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <span class="badge {{ $collecte->statut_tone }}">{{ $collecte->statut_label }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
