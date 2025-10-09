@extends('layouts.app')

@section('title', 'Créer un Itinéraire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"><i class="fas fa-route me-2"></i>Nouvel Itinéraire</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.itineraires.index') }}">Itinéraires</a></li>
                        <li class="breadcrumb-item active">Créer</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de l'itinéraire</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.itineraires.store') }}" method="POST">
                        @csrf
                        
                        <!-- Première ligne -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Collecteur</label>
                                <select name="collecteur_id" class="form-select form-select-sm" required>
                                    <option value="">-- Choisir un collecteur --</option>
                                    @foreach(($collecteurs ?? []) as $collecteur)
                                        <option value="{{ $collecteur->id }}">{{ $collecteur->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control form-control-sm" required>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select form-select-sm" required>
                                    <option value="quotidien">Quotidien</option>
                                    <option value="hebdomadaire">Hebdomadaire</option>
                                    <option value="mensuel">Mensuel</option>
                                    <option value="ponctuel">Ponctuel</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Date début</label>
                                <input type="date" name="date_debut" class="form-control form-control-sm">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="date_fin" class="form-control form-control-sm">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Heure début</label>
                                <input type="time" name="heure_debut" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Deuxième ligne -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Heure fin</label>
                                <input type="time" name="heure_fin" class="form-control form-control-sm">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Description courte">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Distance (km)</label>
                                <input type="number" step="0.1" name="distance_estimee" class="form-control form-control-sm" placeholder="0.0">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Durée (min)</label>
                                <input type="number" name="duree_estimee" class="form-control form-control-sm" placeholder="0">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes optionnelles">
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.itineraires.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save me-1"></i>Créer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Optimisations pour réduire l'espace */
.page-title-box {
    margin-bottom: 1rem !important;
}

.card-header {
    padding: 0.75rem 1rem !important;
}

.card-body {
    padding: 1rem !important;
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.form-control-sm, .form-select-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.mb-3 {
    margin-bottom: 0.75rem !important;
}

/* Responsive */
@media (max-width: 1200px) {
    .col-md-2 {
        flex: 0 0 auto;
        width: 16.66666667%;
    }
}

@media (max-width: 992px) {
    .row .col-md-2,
    .row .col-md-3 {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection