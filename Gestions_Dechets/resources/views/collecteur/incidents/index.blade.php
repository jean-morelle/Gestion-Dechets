@extends('layouts.app')

@section('title', 'Mes incidents')

@section('content')
<div class="page-header">
    <div>
        <h1>Mes incidents</h1>
        <p>Les problèmes que vous avez signalés pendant vos tournées.</p>
    </div>
    <a href="{{ route('collecteur.incidents.create') }}" class="btn btn-outline-danger">
        <i class="fas fa-triangle-exclamation me-1" aria-hidden="true"></i>Signaler un incident
    </a>
</div>

<div class="card">
    @if($incidents->isEmpty())
        <div class="empty-state">
            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
            <p>Aucun incident signalé.</p>
        </div>
    @else
        <ul class="activity-list">
            @foreach($incidents as $incident)
                <li>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('collecteur.incidents.show', $incident) }}" class="d-block">{{ $incident->type_label }}</a>
                        <div class="activity-meta text-truncate">
                            {{ $incident->itineraire->nom ?? 'Tournée supprimée' }} · {{ $incident->created_at->translatedFormat('j M Y, H:i') }}
                        </div>
                        <div class="small text-body-secondary text-truncate">{{ $incident->description }}</div>
                    </div>
                    <span class="badge {{ $incident->statut === 'resolu' ? 'tone-green' : ($incident->statut === 'en_cours' ? 'tone-blue' : 'tone-amber') }}">{{ $incident->statut_label }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="mt-3">{{ $incidents->links() }}</div>
@endsection
