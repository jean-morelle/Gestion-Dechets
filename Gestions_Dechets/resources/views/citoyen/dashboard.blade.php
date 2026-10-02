@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    $s = $statistiques;
    $heure = (int) now()->format('H');
    $salutation = $heure < 18 ? 'Bonjour' : 'Bonsoir';
@endphp

<div class="page-header">
    <div>
        <h1>{{ $salutation }}, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: auth()->user()->name }}</h1>
        <p>{{ ucfirst(now()->translatedFormat('l j F Y')) }}@if(auth()->user()->quartier) · {{ auth()->user()->quartier }}@endif</p>
    </div>
    <a href="{{ route('citoyen.signalements.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i> Nouveau signalement
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('citoyen.signalements.index') }}" class="card stat-card">
            <span class="stat-icon tone-amber"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['signalements']['total'] }}</span>
                <span class="stat-label">Signalements · {{ $s['signalements']['traites'] }} traité{{ $s['signalements']['traites'] > 1 ? 's' : '' }}</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('citoyen.demandes-collecte.index') }}" class="card stat-card">
            <span class="stat-icon tone-green"><i class="fas fa-truck" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['demandes_collecte']['total'] }}</span>
                <span class="stat-label">Demandes · {{ $s['demandes_collecte']['en_attente'] }} en attente</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('citoyen.plaintes.index') }}" class="card stat-card">
            <span class="stat-icon tone-blue"><i class="fas fa-comment-dots" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['plaintes']['total'] }}</span>
                <span class="stat-label">Plaintes · {{ $s['plaintes']['en_attente'] }} en attente</span>
            </span>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('citoyen.notifications.index') }}" class="card stat-card">
            <span class="stat-icon tone-slate"><i class="fas fa-bell" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">{{ $s['notifications']['non_lues'] }}</span>
                <span class="stat-label">Notification{{ $s['notifications']['non_lues'] > 1 ? 's' : '' }} non lue{{ $s['notifications']['non_lues'] > 1 ? 's' : '' }}</span>
            </span>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <h2 class="h6 text-body-secondary text-uppercase mb-3" style="letter-spacing:.05em">Que souhaitez-vous faire ?</h2>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <a href="{{ route('citoyen.signalements.create') }}" class="quick-action">
                    <span class="stat-icon tone-amber"><i class="fas fa-camera" aria-hidden="true"></i></span>
                    <span><strong>Signaler un dépôt</strong><small>Déchets abandonnés, bac plein…</small></span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('citoyen.demandes-collecte.create') }}" class="quick-action">
                    <span class="stat-icon tone-green"><i class="fas fa-truck-ramp-box" aria-hidden="true"></i></span>
                    <span><strong>Demander une collecte</strong><small>Encombrants, déchets verts…</small></span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('citoyen.plaintes.create') }}" class="quick-action">
                    <span class="stat-icon tone-blue"><i class="fas fa-pen" aria-hidden="true"></i></span>
                    <span><strong>Déposer une plainte</strong><small>Service non rendu, retard…</small></span>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Mes dernières démarches</h5>
            </div>
            @if($activites->isEmpty())
                <div class="empty-state">
                    <i class="far fa-folder-open d-block" aria-hidden="true"></i>
                    <p>Vous n'avez encore effectué aucune démarche.</p>
                    <a href="{{ route('citoyen.signalements.create') }}" class="btn btn-outline-primary btn-sm">Faire mon premier signalement</a>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($activites as $a)
                        <li>
                            <span class="stat-icon tone-{{ $a['ton'] }}" style="width:36px;height:36px;font-size:.9rem"><i class="fas {{ $a['icone'] }}" aria-hidden="true"></i></span>
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ $a['lien'] }}" class="d-block text-truncate">{{ $a['titre'] }}</a>
                                <div class="activity-meta text-truncate">{{ $a['detail'] }} · {{ $a['date']->diffForHumans() }}</div>
                            </div>
                            <span class="{{ $a['classe'] }}">{{ $a['statut'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Collectes dans mon quartier</h5>
                <a href="{{ route('citoyen.calendrier.index') }}" class="small text-decoration-none">Calendrier</a>
            </div>
            @if(! auth()->user()->quartier)
                <div class="empty-state">
                    <i class="fas fa-location-dot d-block" aria-hidden="true"></i>
                    <p>Renseignez votre quartier pour voir les jours de passage.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">Compléter mon profil</a>
                </div>
            @elseif($collectesQuartier->isEmpty())
                <div class="empty-state">
                    <i class="far fa-calendar d-block" aria-hidden="true"></i>
                    <p>Aucune collecte programmée pour {{ auth()->user()->quartier }} pour le moment.</p>
                    <a href="{{ route('citoyen.calendrier.index') }}" class="btn btn-outline-primary btn-sm">Voir tout le calendrier</a>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($collectesQuartier as $c)
                        <li>
                            <span class="stat-icon tone-green" style="width:36px;height:36px;font-size:.9rem"><i class="far fa-calendar-check" aria-hidden="true"></i></span>
                            <div class="min-w-0">
                                <div class="fw-semibold">{{ $c->frequence === 'quotidienne' ? 'Tous les jours' : $c->jour_semaine_label }}</div>
                                <div class="activity-meta">
                                    {{ $c->type_collecte_label }}
                                    @if($c->heure_debut) · {{ $c->heure_debut->format('H:i') }}@if($c->heure_fin)–{{ $c->heure_fin->format('H:i') }}@endif @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
