@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Tableau de Bord</h2>
                    <p class="text-muted mb-0">Bienvenue {{ auth()->user()->name }} !</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-danger fs-6">Administrateur</span>
                    <span class="badge bg-info fs-6">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <!-- Utilisateurs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Utilisateurs Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['utilisateurs']['total'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signalements -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Signalements
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['signalements']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['signalements']['en_attente'] }} en attente
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plaintes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Plaintes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['plaintes']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['plaintes']['en_attente'] }} en attente
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comment-dots fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itinéraires -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Itinéraires
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['itineraires']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['itineraires']['actifs'] }} actifs
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row mb-4">
        <!-- Campagnes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Campagnes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['campagnes']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['campagnes']['actives'] }} actives
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collectes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Collectes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['collectes']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['collectes']['terminees'] }} terminées
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-recycle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendriers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Calendriers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['calendriers']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['calendriers']['actifs'] }} actifs
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Notifications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statistiques['notifications']['total'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $statistiques['notifications']['non_lues'] }} non lues
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Évolution des 7 derniers jours -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Activité des 7 derniers jours</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-right">
                                <h4 class="text-warning">{{ $statistiques['evolution']['signalements'] }}</h4>
                                <p class="mb-0">Signalements</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-right">
                                <h4 class="text-info">{{ $statistiques['evolution']['plaintes'] }}</h4>
                                <p class="mb-0">Plaintes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-right">
                                <h4 class="text-success">{{ $statistiques['evolution']['collectes'] }}</h4>
                                <p class="mb-0">Collectes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-secondary">{{ $statistiques['evolution']['campagnes'] }}</h4>
                            <p class="mb-0">Campagnes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-users fa-lg mb-2"></i><br>
                                <small>Utilisateurs</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.signalements.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-exclamation-triangle fa-lg mb-2"></i><br>
                                <small>Signalements</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.plaintes.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-comment-dots fa-lg mb-2"></i><br>
                                <small>Plaintes</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.itineraires.index') }}" class="btn btn-success w-100">
                                <i class="fas fa-route fa-lg mb-2"></i><br>
                                <small>Itinéraires</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.campagnes.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-bullhorn fa-lg mb-2"></i><br>
                                <small>Campagnes</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.calendrier.index') }}" class="btn btn-dark w-100">
                                <i class="fas fa-calendar-alt fa-lg mb-2"></i><br>
                                <small>Calendriers</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu récent -->
    <div class="row">
        <!-- Signalements récents -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Signalements Récents</h6>
                    <a href="{{ route('admin.signalements.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($signalementsRecents as $signalement)
                    <div class="d-flex align-items-center mb-3 p-2 border rounded">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $signalement->type_dechet_label ?? 'Signalement' }}</h6>
                            <small class="text-muted">
                                Par {{ $signalement->user->name ?? 'Anonyme' }} • 
                                {{ $signalement->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $signalement->statut === 'traite' ? 'success' : 'warning' }}">
                                {{ ucfirst($signalement->statut) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Aucun signalement récent</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Campagnes récentes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Campagnes Récentes</h6>
                    <a href="{{ route('admin.campagnes.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($campagnesRecentes as $campagne)
                    <div class="d-flex align-items-center mb-3 p-2 border rounded">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bullhorn text-secondary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ Str::limit($campagne->titre, 30) }}</h6>
                            <small class="text-muted">
                                {{ $campagne->created_at->format('d/m/Y H:i') }} • 
                                {{ $campagne->type_label ?? ucfirst($campagne->type) }}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $campagne->statut === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($campagne->statut) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Aucune campagne récente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.border-left-dark {
    border-left: 0.25rem solid #5a5c69 !important;
}
.text-xs {
    font-size: 0.7rem;
}
.font-weight-bold {
    font-weight: 700 !important;
}
.text-uppercase {
    text-transform: uppercase !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endsection
