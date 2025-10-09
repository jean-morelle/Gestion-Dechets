@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Utilisateur #{{ $user->id }}</h6>
                    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <!-- Informations principales en 3 colonnes -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6 class="text-muted small">Nom complet</h6>
                            <p class="mb-1">{{ $user->name }}</p>

                            <h6 class="text-muted small">Email</h6>
                            <p class="mb-1">{{ $user->email }}</p>

                            <h6 class="text-muted small">Téléphone</h6>
                            <p class="mb-1">{{ $user->telephone ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Rôle</h6>
                            <p class="mb-1">
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'collecteur' ? 'warning' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>

                            <h6 class="text-muted small">Statut</h6>
                            <p class="mb-1">
                                <span class="badge bg-{{ $user->statut == 'actif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->statut) }}
                                </span>
                            </p>

                            <h6 class="text-muted small">Date de création</h6>
                            <p class="mb-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Adresse</h6>
                            <p class="mb-1">{{ $user->adresse ?? 'Non spécifiée' }}</p>

                            <h6 class="text-muted small">Quartier</h6>
                            <p class="mb-1">{{ $user->quartier ?? 'Non spécifié' }}</p>

                            <h6 class="text-muted small">Dernière connexion</h6>
                            <p class="mb-1">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($user->photo)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted small">Photo de profil</h6>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $user->photo) }}"
                                     alt="Photo de profil"
                                     class="img-fluid rounded"
                                     style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions d'administration compactes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0 small"><i class="fas fa-cogs me-1"></i> Actions d'Administration</h6>
                                </div>
                                <div class="card-body py-2">
                                    <form method="POST" action="{{ route('admin.utilisateurs.statut', $user->id) }}" class="row g-2">
                                        @csrf
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Changer le statut</label>
                                            <select name="statut" class="form-select form-select-sm">
                                                <option value="actif" {{ $user->statut === 'actif' ? 'selected' : '' }}>Actif</option>
                                                <option value="inactif" {{ $user->statut === 'inactif' ? 'selected' : '' }}>Inactif</option>
                                                <option value="suspendu" {{ $user->statut === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Changer le rôle</label>
                                            <select name="role" class="form-select form-select-sm">
                                                <option value="citoyen" {{ $user->role === 'citoyen' ? 'selected' : '' }}>Citoyen</option>
                                                <option value="collecteur" {{ $user->role === 'collecteur' ? 'selected' : '' }}>Collecteur</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <label class="form-label small">Commentaires</label>
                                            <input type="text" name="commentaires" class="form-control form-control-sm" 
                                                   placeholder="Commentaires admin...">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label small">Actions</label>
                                            <div class="d-flex gap-1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <a href="{{ route('admin.utilisateurs.destroy', $user->id) }}" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Supprimer cet utilisateur ?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques de l'utilisateur -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0 small"><i class="fas fa-chart-bar me-1"></i> Statistiques</h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h6 class="text-muted small">Signalements</h6>
                                            <p class="mb-0">{{ $user->signalements()->count() }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-muted small">Plaintes</h6>
                                            <p class="mb-0">{{ $user->plaintes()->count() }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-muted small">Notifications</h6>
                                            <p class="mb-0">{{ $user->notifications()->count() }}</p>
                                        </div>
                                    </div>
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
