@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Mes Incidents
                </h4>
                <p class="text-muted">Historique de tous les incidents que vous avez signalés.</p>
            </div>
        </div>
    </div>


    <!-- Liste des incidents -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des Incidents</h5>
                </div>
                <div class="card-body">
                    @forelse($incidents ?? [] as $incident)
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x {{ $incident->gravite === 'critique' ? 'text-danger' : ($incident->gravite === 'elevee' ? 'text-warning' : 'text-info') }}"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $incident->titre ?? 'Incident #' . $incident->id }}</h6>
                                    <p class="text-muted mb-1">{{ Str::limit($incident->description, 100) }}</p>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $incident->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <span class="badge {{ $incident->statut === 'resolu' ? 'bg-success' : ($incident->statut === 'en_traitement' ? 'bg-primary' : ($incident->statut === 'ouvert' ? 'bg-warning' : 'bg-secondary')) }}">
                                            {{ $incident->statut_label ?? 'Ouvert' }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge {{ $incident->gravite === 'critique' ? 'bg-danger' : ($incident->gravite === 'elevee' ? 'bg-warning' : ($incident->gravite === 'moyenne' ? 'bg-info' : 'bg-secondary')) }}">
                                            {{ $incident->gravite_label ?? 'Normale' }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $incident->type_label ?? 'Non spécifié' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('collecteur.incidents.show', $incident->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun incident signalé</p>
                        <a href="{{ route('collecteur.incidents.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>Signaler le premier incident
                        </a>
                    </div>
                    @endforelse

                    @if(isset($incidents) && $incidents->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $incidents->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




















