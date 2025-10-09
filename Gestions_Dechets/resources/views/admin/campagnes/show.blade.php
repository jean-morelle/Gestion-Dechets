@extends('layouts.app')

@section('title', 'Détails de la Campagne')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">{{ $campagne->titre }}</h4>
                    <p class="text-muted mb-0">Détails de la campagne de sensibilisation</p>
                </div>
                <div>
                    <a href="{{ route('admin.campagnes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                    <a href="{{ route('admin.campagnes.edit', $campagne) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Contenu principal -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Contenu de la campagne</h5>
                        </div>
                        <div class="card-body">
                            <!-- Image de prévisualisation -->
                            @if($campagne->image_preview)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/' . $campagne->image_preview) }}" 
                                         alt="{{ $campagne->titre }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 300px;">
                                </div>
                            @endif

                            <!-- Description -->
                            <div class="mb-4">
                                <h6 class="text-primary">Description</h6>
                                <p>{{ $campagne->description }}</p>
                            </div>

                            <!-- Contenu selon le type -->
                            @if($campagne->type === 'affiche' || $campagne->type === 'infographie')
                                @if($campagne->fichier)
                                    <div class="mb-4">
                                        <h6 class="text-primary">Fichier</h6>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $campagne->fichier) }}" 
                                                 alt="{{ $campagne->titre }}" 
                                                 class="img-fluid rounded border" 
                                                 style="max-height: 500px;">
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="{{ asset('storage/' . $campagne->fichier) }}" 
                                               class="btn btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @elseif($campagne->type === 'video')
                                @if($campagne->url_video)
                                    <div class="mb-4">
                                        <h6 class="text-primary">Vidéo</h6>
                                        <div class="ratio ratio-16x9">
                                            <iframe src="{{ $campagne->url_video }}" 
                                                    title="{{ $campagne->titre }}" 
                                                    allowfullscreen></iframe>
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="{{ $campagne->url_video }}" 
                                               class="btn btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt me-1"></i>Ouvrir dans un nouvel onglet
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @elseif($campagne->type === 'message')
                                @if($campagne->contenu_message)
                                    <div class="mb-4">
                                        <h6 class="text-primary">Message</h6>
                                        <div class="bg-light p-3 rounded">
                                            <p class="mb-0">{{ $campagne->contenu_message }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations secondaires -->
                <div class="col-md-4">
                    <!-- Statut et actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Statut et Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Statut :</strong>
                                <span class="badge bg-{{ $campagne->statut_class }} ms-2">
                                    {{ $campagne->statut_label }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Type :</strong>
                                <span class="badge bg-secondary ms-2">
                                    {{ $campagne->type_label }}
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                @if($campagne->statut === 'brouillon')
                                    <form action="{{ route('admin.campagnes.publier', $campagne) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-play me-1"></i>Publier
                                        </button>
                                    </form>
                                @elseif($campagne->statut === 'active')
                                    <form action="{{ route('admin.campagnes.archiver', $campagne) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-archive me-1"></i>Archiver
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.campagnes.edit', $campagne) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                                
                                <a href="{{ route('admin.campagnes.statistiques', $campagne) }}" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-1"></i>Statistiques
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de diffusion -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Période de diffusion</h6>
                        </div>
                        <div class="card-body">
                            @if($campagne->date_debut)
                                <div class="mb-2">
                                    <strong>Début :</strong>
                                    <span class="text-muted">{{ $campagne->date_debut->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if($campagne->date_fin)
                                <div class="mb-2">
                                    <strong>Fin :</strong>
                                    <span class="text-muted">{{ $campagne->date_fin->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if(!$campagne->date_debut && !$campagne->date_fin)
                                <p class="text-muted mb-0">Diffusion permanente</p>
                            @endif
                        </div>
                    </div>

                    <!-- Quartiers cibles -->
                    @if($campagne->quartiers_cibles && count($campagne->quartiers_cibles) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Quartiers cibles</h6>
                            </div>
                            <div class="card-body">
                                @foreach($campagne->quartiers_cibles as $quartier)
                                    <span class="badge bg-primary me-1 mb-1">{{ $quartier }}</span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Ciblage</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-0">Tous les quartiers</p>
                            </div>
                        </div>
                    @endif

                    <!-- Statistiques -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Statistiques</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $campagne->vues }}</h4>
                                    <small class="text-muted">Vues</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $campagne->partages }}</h4>
                                    <small class="text-muted">Partages</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection















