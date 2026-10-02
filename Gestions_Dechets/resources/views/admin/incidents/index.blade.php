@extends('layouts.app')

@section('title', 'Incidents')

@section('content')
<div class="page-header">
    <div>
        <h1>Incidents</h1>
        <p>Problèmes signalés par les collecteurs sur le terrain.</p>
    </div>
</div>

<ul class="nav nav-pills small mb-3">
    @foreach(['ouverts' => 'À traiter', 'resolus' => 'Clos', 'tous' => 'Tous'] as $valeur => $libelle)
        <li class="nav-item">
            <a class="nav-link {{ $statut === $valeur ? 'active' : '' }}" href="{{ route('admin.incidents.index', ['statut' => $valeur]) }}">
                {{ $libelle }}
                @if($valeur === 'ouverts' && $ouverts > 0)<span class="badge rounded-pill text-bg-light ms-1">{{ $ouverts }}</span>@endif
            </a>
        </li>
    @endforeach
</ul>

<div class="card">
    @if($incidents->isEmpty())
        <div class="empty-state">
            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
            <p>{{ $statut === 'ouverts' ? 'Aucun incident en cours.' : 'Aucun incident.' }}</p>
        </div>
    @else
        <ul class="activity-list">
            @foreach($incidents as $incident)
                <li>
                    <span class="stat-icon {{ in_array($incident->priorite, ['urgente', 'elevee']) ? 'tone-red' : 'tone-amber' }}" aria-hidden="true">
                        <i class="fas {{ $incident->type_incident === 'panne_vehicule' ? 'fa-truck' : 'fa-triangle-exclamation' }}"></i>
                    </span>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('admin.incidents.show', $incident) }}" class="d-block">{{ $incident->type_label }}</a>
                        <div class="activity-meta text-truncate">
                            {{ $incident->collecteur->name ?? 'Collecteur supprimé' }} · {{ $incident->itineraire->nom ?? 'Tournée supprimée' }} · {{ $incident->created_at->diffForHumans() }}
                        </div>
                        <div class="small text-body-secondary text-truncate">{{ $incident->description }}</div>
                    </div>
                    <span class="badge {{ $incident->statut === 'resolu' ? 'tone-green' : ($incident->statut === 'en_cours' ? 'tone-blue' : ($incident->statut === 'annule' ? 'tone-slate' : 'tone-amber')) }}">{{ $incident->statut_label }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="mt-3">{{ $incidents->links() }}</div>
@endsection
