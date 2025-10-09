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
                    <p class="text-muted mb-0">Campagne de sensibilisation</p>
                </div>
                <div>
                    <a href="{{ route('citoyen.campagnes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
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

                <!-- Actions et informations -->
                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form action="{{ route('citoyen.campagnes.partager', $campagne) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-share me-1"></i>Partager cette campagne
                                    </button>
                                </form>
                                
                                @if($campagne->fichier)
                                    <a href="{{ asset('storage/' . $campagne->fichier) }}" 
                                       class="btn btn-outline-primary" 
                                       target="_blank">
                                        <i class="fas fa-download me-1"></i>Télécharger
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informations</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Type :</strong></td>
                                    <td><span class="badge bg-secondary">{{ $campagne->type_label }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td><span class="badge bg-{{ $campagne->statut_class }}">{{ $campagne->statut_label }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Publiée le :</strong></td>
                                    <td>{{ $campagne->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                @if($campagne->date_debut)
                                    <tr>
                                        <td><strong>Début :</strong></td>
                                        <td>{{ $campagne->date_debut->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                                @if($campagne->date_fin)
                                    <tr>
                                        <td><strong>Fin :</strong></td>
                                        <td>{{ $campagne->date_fin->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            </table>
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