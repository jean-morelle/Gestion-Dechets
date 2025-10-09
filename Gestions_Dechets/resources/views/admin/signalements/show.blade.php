@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Signalement #{{ $signalement->id }}</h6>
                    <a href="{{ route('admin.signalements.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <!-- Informations principales en 3 colonnes -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6 class="text-muted small">Type de déchet</h6>
                            <p class="mb-1">{{ $signalement->type_dechet_label ?? 'Non spécifié' }}</p>

                            <h6 class="text-muted small">Description</h6>
                            <p class="mb-1">{{ Str::limit($signalement->description, 50) }}</p>

                            <h6 class="text-muted small">Adresse</h6>
                            <p class="mb-1">{{ Str::limit($signalement->adresse ?? 'Non spécifié', 30) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Quartier</h6>
                            <p class="mb-1">{{ $signalement->quartier ?? 'Non spécifié' }}</p>

                            <h6 class="text-muted small">Téléphone</h6>
                            <p class="mb-1">{{ $signalement->contact_telephone ?? 'Non spécifié' }}</p>

                            <h6 class="text-muted small">Citoyen</h6>
                            <p class="mb-1">{{ $signalement->user->name ?? 'Utilisateur inconnu' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Statut</h6>
                            <p class="mb-1">
                                <span class="badge {{ $signalement->statut === 'traite' ? 'bg-success' : ($signalement->statut === 'en_cours' ? 'bg-primary' : ($signalement->statut === 'en_attente' ? 'bg-warning' : 'bg-secondary')) }}">
                                    {{ $signalement->statut_label ?? 'En attente' }}
                                </span>
                            </p>

                            <h6 class="text-muted small">Priorité</h6>
                            <p class="mb-1">
                                <span class="badge {{ $signalement->priorite_class ?? '' }}">{{ $signalement->priorite_label ?? 'Moyenne' }}</span>
                            </p>

                            <h6 class="text-muted small">Date</h6>
                            <p class="mb-1">{{ $signalement->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Coordonnées GPS et Photo sur une ligne -->
                    <div class="row mb-3">
                        @if($signalement->latitude && $signalement->longitude)
                        <div class="col-md-6">
                            <h6 class="text-muted small">Position GPS</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Latitude:</small>
                                    <p class="mb-0 small">{{ $signalement->latitude }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Longitude:</small>
                                    <p class="mb-0 small">{{ $signalement->longitude }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($signalement->photo)
                        <div class="col-md-6">
                            <h6 class="text-muted small">Photo</h6>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $signalement->photo) }}"
                                     alt="Photo"
                                     class="img-fluid rounded"
                                     style="max-height: 150px;">
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Actions d'administration compactes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0 small"><i class="fas fa-cogs me-1"></i> Actions</h6>
                                </div>
                                <div class="card-body py-2">
                                    <form method="POST" action="{{ route('admin.signalements.update', $signalement->id) }}" class="row g-2">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Statut</label>
                                            <select name="statut" class="form-select form-select-sm">
                                                <option value="en_attente" {{ $signalement->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                <option value="en_cours" {{ $signalement->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="traite" {{ $signalement->statut === 'traite' ? 'selected' : '' }}>Traités</option>
                                                <option value="rejete" {{ $signalement->statut === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Priorité</label>
                                            <select name="priorite" class="form-select form-select-sm">
                                                <option value="faible" {{ $signalement->priorite === 'faible' ? 'selected' : '' }}>Faible</option>
                                                <option value="moyenne" {{ $signalement->priorite === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                                <option value="elevee" {{ $signalement->priorite === 'elevee' ? 'selected' : '' }}>Élevée</option>
                                                <option value="urgente" {{ $signalement->priorite === 'urgente' ? 'selected' : '' }}>Urgente</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <label class="form-label small">Commentaires admin</label>
                                            <input type="text" name="commentaires_admin" class="form-control form-control-sm" 
                                                   value="{{ $signalement->commentaires_admin }}" placeholder="Commentaires admin...">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label small">Actions</label>
                                            <div class="d-flex gap-1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <a href="{{ route('admin.signalements.destroy', $signalement->id) }}" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Êtes-vous sûr ?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
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
