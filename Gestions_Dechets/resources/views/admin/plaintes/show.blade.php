@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Plaintes #{{ $plainte->id }}</h6>
                    <a href="{{ route('admin.plaintes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <!-- Informations principales en 3 colonnes -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6 class="text-muted small">Type de plainte</h6>
                            <p class="mb-1">
                                <span class="badge bg-info">{{ $plainte->type_plainte_label ?? ucfirst(str_replace('_', ' ', $plainte->type_plainte)) }}</span>
                            </p>

                            <h6 class="text-muted small">Objet</h6>
                            <p class="mb-1">{{ $plainte->sujet ?? $plainte->objet ?? __('app.not_specified') }}</p>

                            <h6 class="text-muted small">{{ __('complaints.description') }}</h6>
                            <p class="mb-1">{{ Str::limit($plainte->description, 100) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Statut</h6>
                            <p class="mb-1">
                                <span class="badge {{ $plainte->statut === 'traite' ? 'bg-success' : ($plainte->statut === 'en_cours' ? 'bg-primary' : ($plainte->statut === 'en_attente' ? 'bg-warning' : 'bg-secondary')) }}">
                                    {{ $plainte->statut_label ?? ucfirst(str_replace('_', ' ', $plainte->statut)) }}
                                </span>
                            </p>

                            <h6 class="text-muted small">Priorité</h6>
                            <p class="mb-1">
                                <span class="badge {{ $plainte->priorite === 'elevee' ? 'bg-danger' : ($plainte->priorite === 'moyenne' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ $plainte->priorite_label ?? ucfirst($plainte->priorite) }}
                                </span>
                            </p>

                            <h6 class="text-muted small">Plaintes Date</h6>
                            <p class="mb-1">{{ $plainte->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small">Citoyen</h6>
                            <p class="mb-1">{{ $plainte->user->name ?? __('app.unknown_user') ?? 'Utilisateur inconnu' }}</p>

                            <h6 class="text-muted small">Email</h6>
                            <p class="mb-1">{{ $plainte->user->email ?? __('app.not_specified') }}</p>

                            <h6 class="text-muted small">Téléphone</h6>
                            <p class="mb-1">{{ $plainte->user->telephone ?? __('app.not_specified') }}</p>
                        </div>
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted small">Quartier</h6>
                            <p class="mb-1">{{ $plainte->user->quartier ?? __('app.not_specified') }}</p>

                            <h6 class="text-muted small">{{ __('app.registration_date') ?? 'Membre depuis' }}</h6>
                            <p class="mb-1">{{ $plainte->user->created_at->format('m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small">Plaintes Total</h6>
                            <p class="mb-1">{{ $plainte->user->plaintes()->count() }} Plaintes</p>

                            @if($plainte->date_traitement)
                            <h6 class="text-muted small">{{ __('complaints.treatment_date') }}</h6>
                            <p class="mb-1">{{ $plainte->date_traitement->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($plainte->reponse_admin)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white py-2">
                                    <h6 class="mb-0 small"><i class="fas fa-reply me-1"></i> {{ __('complaints.admin_response') }}</h6>
                                </div>
                                <div class="card-body py-2">
                                    <p class="mb-0">{{ $plainte->reponse_admin }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions d'administration compactes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0 small"><i class="fas fa-cogs me-1"></i> Actions</h6>
                                </div>
                                <div class="card-body py-2">
                                    <form method="POST" action="{{ route('admin.plaintes.traiter', $plainte) }}" class="row g-2">
                                        @csrf
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Statut</label>
                                            <select name="statut" class="form-select form-select-sm">
                                                <option value="en_attente" {{ $plainte->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                <option value="en_cours" {{ $plainte->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="traite" {{ $plainte->statut === 'traite' ? 'selected' : '' }}>Traitées</option>
                                                <option value="rejete" {{ $plainte->statut === 'rejete' ? 'selected' : '' }}>{{ __('app.rejected') }}</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="form-label small">{{ __('complaints.admin_response') }}</label>
                                            <input type="text" name="reponse" class="form-control form-control-sm" 
                                                   value="{{ $plainte->reponse_admin }}" placeholder="{{ __('complaints.admin_response') }}...">
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label class="form-label small">Actions</label>
                                            <div class="d-flex gap-1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <a href="{{ route('admin.plaintes.destroy', $plainte->id) }}" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{{ __('app.confirm') }}')">
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