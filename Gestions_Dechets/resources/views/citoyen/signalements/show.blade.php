@extends('layouts.app')


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                </div>
                <div>
                    <a href="{{ route('citoyen.signalements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Informations du signalement -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                Signalement #{{ $signalement->id }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Type de déchet</h6>
                                    <p class="mb-3">
                                        <span class="badge bg-info">{{ $signalement->type_dechet_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Description</h6>
                                    <p class="mb-3">{{ $signalement->description }}</p>

                                    <h6 class="text-muted">Adresse</h6>
                                    <p class="mb-3">{{ $signalement->adresse }}</p>

                                    <h6 class="text-muted">Quartier</h6>
                                    <p class="mb-3">{{ $signalement->quartier }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Statut</h6>
                                    <p class="mb-3">
                                        <span class="badge {{ $signalement->statut_class }}">{{ $signalement->statut_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Priorité</h6>
                                    <p class="mb-3">
                                        <span class="badge {{ $signalement->priorite_class }}">{{ $signalement->priorite_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Date (Créé le)</h6>
                                    <p class="mb-3">{{ $signalement->created_at->format('d/m/Y à H:i') }}</p>

                                    <h6 class="text-muted">Dernière mise à jour</h6>
                                    <p class="mb-3">{{ $signalement->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>

                            <!-- Coordonnées GPS -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="text-muted">Position GPS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Latitude:</small>
                                            <p class="mb-0">{{ $signalement->latitude }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Longitude:</small>
                                            <p class="mb-0">{{ $signalement->longitude }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo -->
                            @if($signalement->photo)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="text-muted">Photo</h6>
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $signalement->photo) }}" 
                                             alt="Photo" 
                                             class="img-fluid rounded" 
                                             style="max-height: 300px;">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations de collecte -->
                    @if($signalement->date_collecte_prevue || $signalement->date_collecte_reelle)
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar"></i>
                                Collectes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($signalement->date_collecte_prevue)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Date prévue</h6>
                                    <p class="mb-3">{{ \Carbon\Carbon::parse($signalement->date_collecte_prevue)->format('d/m/Y à H:i') }}</p>
                                </div>
                                @endif
                                @if($signalement->date_collecte_reelle)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Date</h6>
                                    <p class="mb-3">{{ \Carbon\Carbon::parse($signalement->date_collecte_reelle)->format('d/m/Y à H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes de l'administrateur -->
                    @if($signalement->notes_admin)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-comment"></i>
                                {{ __('app.admin_comments') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $signalement->notes_admin }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">


                </div>
            </div>
        </div>
    </div>
</div>
@endsection







@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                </div>
                <div>
                    <a href="{{ route('citoyen.signalements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Informations du signalement -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                Signalement #{{ $signalement->id }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Type de déchet</h6>
                                    <p class="mb-3">
                                        <span class="badge bg-info">{{ $signalement->type_dechet_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Description</h6>
                                    <p class="mb-3">{{ $signalement->description }}</p>

                                    <h6 class="text-muted">Adresse</h6>
                                    <p class="mb-3">{{ $signalement->adresse }}</p>

                                    <h6 class="text-muted">Quartier</h6>
                                    <p class="mb-3">{{ $signalement->quartier }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Statut</h6>
                                    <p class="mb-3">
                                        <span class="badge {{ $signalement->statut_class }}">{{ $signalement->statut_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Priorité</h6>
                                    <p class="mb-3">
                                        <span class="badge {{ $signalement->priorite_class }}">{{ $signalement->priorite_label }}</span>
                                    </p>

                                    <h6 class="text-muted">Date de création</h6>
                                    <p class="mb-3">{{ $signalement->created_at->format('d/m/Y à H:i') }}</p>

                                    <h6 class="text-muted">Dernière mise à jour</h6>
                                    <p class="mb-3">{{ $signalement->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>

                            <!-- Coordonnées GPS -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="text-muted">Coordonnées GPS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Latitude:</small>
                                            <p class="mb-0">{{ $signalement->latitude }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Longitude:</small>
                                            <p class="mb-0">{{ $signalement->longitude }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Photo -->
                            @if($signalement->photo)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="text-muted">Photo jointe</h6>
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $signalement->photo) }}" 
                                             alt="Photo du signalement" 
                                             class="img-fluid rounded" 
                                             style="max-height: 300px;">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations de collecte -->
                    @if($signalement->date_collecte_prevue || $signalement->date_collecte_reelle)
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar"></i>
                                Informations de Collecte
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($signalement->date_collecte_prevue)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Date prévue</h6>
                                    <p class="mb-3">{{ \Carbon\Carbon::parse($signalement->date_collecte_prevue)->format('d/m/Y à H:i') }}</p>
                                </div>
                                @endif
                                @if($signalement->date_collecte_reelle)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Date réelle</h6>
                                    <p class="mb-3">{{ \Carbon\Carbon::parse($signalement->date_collecte_reelle)->format('d/m/Y à H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes de l'administrateur -->
                    @if($signalement->notes_admin)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-comment"></i>
                                Notes de l'Administrateur
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $signalement->notes_admin }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">


                </div>
            </div>
        </div>
    </div>
</div>
@endsection






