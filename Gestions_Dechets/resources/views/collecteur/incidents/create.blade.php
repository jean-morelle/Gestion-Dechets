@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Signaler un Incident</h6>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="{{ route('collecteur.incidents.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="titre" class="form-label small">Titre de l'incident <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('titre') is-invalid @enderror" 
                                           id="titre" name="titre" value="{{ old('titre') }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="type" class="form-label small">Type d'incident <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="panne_vehicule" {{ old('type') === 'panne_vehicule' ? 'selected' : '' }}>Panne de véhicule</option>
                                        <option value="probleme_route" {{ old('type') === 'probleme_route' ? 'selected' : '' }}>Problème de route</option>
                                        <option value="conflit_citoyen" {{ old('type') === 'conflit_citoyen' ? 'selected' : '' }}>Conflit avec citoyen</option>
                                        <option value="equipement_defaillant" {{ old('type') === 'equipement_defaillant' ? 'selected' : '' }}>Équipement défaillant</option>
                                        <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="gravite" class="form-label small">Niveau de gravité <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('gravite') is-invalid @enderror" id="gravite" name="gravite" required>
                                        <option value="">Sélectionnez un niveau</option>
                                        <option value="faible" {{ old('gravite') === 'faible' ? 'selected' : '' }}>Faible</option>
                                        <option value="moyenne" {{ old('gravite') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="elevee" {{ old('gravite') === 'elevee' ? 'selected' : '' }}>Élevée</option>
                                        <option value="critique" {{ old('gravite') === 'critique' ? 'selected' : '' }}>Critique</option>
                                    </select>
                                    @error('gravite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="urgence" class="form-label small">Niveau d'urgence</label>
                                    <select class="form-select form-select-sm @error('urgence') is-invalid @enderror" id="urgence" name="urgence">
                                        <option value="normale" {{ old('urgence') === 'normale' ? 'selected' : '' }}>Normale</option>
                                        <option value="elevee" {{ old('urgence') === 'elevee' ? 'selected' : '' }}>Élevée</option>
                                        <option value="urgente" {{ old('urgence') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                    @error('urgence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label for="description" class="form-label small">Description détaillée <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="localisation" class="form-label small">Localisation</label>
                                    <input type="text" class="form-control form-control-sm @error('localisation') is-invalid @enderror" 
                                           id="localisation" name="localisation" value="{{ old('localisation') }}">
                                    @error('localisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="quartier" class="form-label small">Quartier</label>
                                    <input type="text" class="form-control form-control-sm @error('quartier') is-invalid @enderror" 
                                           id="quartier" name="quartier" value="{{ old('quartier') }}">
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="impact_collecte" class="form-label small">Impact sur la collecte</label>
                                    <select class="form-select form-select-sm @error('impact_collecte') is-invalid @enderror" id="impact_collecte" name="impact_collecte">
                                        <option value="aucun" {{ old('impact_collecte') === 'aucun' ? 'selected' : '' }}>Aucun impact</option>
                                        <option value="retard" {{ old('impact_collecte') === 'retard' ? 'selected' : '' }}>Retard mineur</option>
                                        <option value="interruption" {{ old('impact_collecte') === 'interruption' ? 'selected' : '' }}>Interruption temporaire</option>
                                        <option value="annulation" {{ old('impact_collecte') === 'annulation' ? 'selected' : '' }}>Annulation de collecte</option>
                                    </select>
                                    @error('impact_collecte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="actions_prises" class="form-label small">Actions déjà prises</label>
                                    <input type="text" class="form-control form-control-sm @error('actions_prises') is-invalid @enderror" 
                                           id="actions_prises" name="actions_prises" value="{{ old('actions_prises') }}">
                                    @error('actions_prises')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="besoin_assistance" class="form-label small">Besoin d'assistance</label>
                                    <select class="form-select form-select-sm @error('besoin_assistance') is-invalid @enderror" id="besoin_assistance" name="besoin_assistance">
                                        <option value="non" {{ old('besoin_assistance') === 'non' ? 'selected' : '' }}>Non</option>
                                        <option value="technique" {{ old('besoin_assistance') === 'technique' ? 'selected' : '' }}>Assistance technique</option>
                                        <option value="mecanique" {{ old('besoin_assistance') === 'mecanique' ? 'selected' : '' }}>Assistance mécanique</option>
                                        <option value="administrative" {{ old('besoin_assistance') === 'administrative' ? 'selected' : '' }}>Assistance administrative</option>
                                        <option value="urgence" {{ old('besoin_assistance') === 'urgence' ? 'selected' : '' }}>Assistance d'urgence</option>
                                    </select>
                                    @error('besoin_assistance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Signaler
                            </button>
                            <a href="{{ route('collecteur.incidents.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection