@extends('layouts.app')

@section('title', __('routes.my_routes') ?? 'Mes Itinéraires')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"><i class="fas fa-route me-2"></i>Mes Itinéraires</h4>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse(($itineraires ?? []) as $itineraire)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $itineraire->nom }}</h5>
                            <span class="badge {{ $itineraire->statut_class ?? 'bg-info' }}">{{ $itineraire->statut_label ?? ucfirst($itineraire->statut) }}</span>
                        </div>
                        <p class="text-muted mb-1">Type: {{ $itineraire->type_label ?? ucfirst($itineraire->type) }}</p>
                        <p class="text-muted mb-1">Dates: {{ $itineraire->date_debut?->format('d/m/Y') }} - {{ $itineraire->date_fin?->format('d/m/Y') }}</p>
                        <p class="text-muted">Heures: {{ $itineraire->heure_debut?->format('H:i') }} - {{ $itineraire->heure_fin?->format('H:i') }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 d-flex gap-2">
                        <a href="{{ route('collecteur.itineraires.show', $itineraire->id) }}" class="btn btn-outline-primary btn-sm">Ouvrir</a>
                        <form action="{{ route('collecteur.itineraires.demarrer', $itineraire->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-primary btn-sm" {{ $itineraire->statut === 'en_cours' ? 'disabled' : '' }}>Démarrer</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun itinéraire pour l'instant.
                    <br>
                    <small class="text-muted">
                        Les itinéraires vous seront assignés par l'administrateur.
                    </small>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
















