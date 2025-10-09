@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Créer un nouveau calendrier de collecte</h6>
                    <a href="{{ route('admin.calendrier.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="{{ route('admin.calendrier.store') }}">
                        @csrf
                        
                        <!-- Ligne 1: Nom et Quartier -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="nom" class="form-label small">Nom du calendrier</label>
                                    <input type="text" class="form-control form-control-sm @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom') }}" 
                                           placeholder="Laissé vide pour génération automatique">
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Si vide, un nom sera généré automatiquement</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="quartier" class="form-label small">Quartier <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('quartier') is-invalid @enderror" 
                                           id="quartier" name="quartier" value="{{ old('quartier') }}" required>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 2: Type et Fréquence -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="type_collecte" class="form-label small">Type de collecte <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('type_collecte') is-invalid @enderror" 
                                            id="type_collecte" name="type_collecte" required>
                                        <option value="">Sélectionner le type</option>
                                        <option value="menagere" {{ old('type_collecte') == 'menagere' ? 'selected' : '' }}>Ménagère</option>
                                        <option value="encombrant" {{ old('type_collecte') == 'encombrant' ? 'selected' : '' }}>Encombrant</option>
                                        <option value="vert" {{ old('type_collecte') == 'vert' ? 'selected' : '' }}>Vert</option>
                                        <option value="recyclage" {{ old('type_collecte') == 'recyclage' ? 'selected' : '' }}>Recyclage</option>
                                    </select>
                                    @error('type_collecte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="frequence" class="form-label small">Fréquence <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('frequence') is-invalid @enderror" 
                                            id="frequence" name="frequence" required>
                                        <option value="">Sélectionner la fréquence</option>
                                        <option value="quotidienne" {{ old('frequence') == 'quotidienne' ? 'selected' : '' }}>Quotidienne</option>
                                        <option value="hebdomadaire" {{ old('frequence') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                        <option value="mensuelle" {{ old('frequence') == 'mensuelle' ? 'selected' : '' }}>Mensuelle</option>
                                        <option value="ponctuelle" {{ old('frequence') == 'ponctuelle' ? 'selected' : '' }}>Ponctuelle</option>
                                    </select>
                                    @error('frequence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 3: Jour, Heure début, Heure fin -->
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="jour_semaine" class="form-label small">Jour de la semaine <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('jour_semaine') is-invalid @enderror" 
                                            id="jour_semaine" name="jour_semaine" required>
                                        <option value="">Sélectionner le jour</option>
                                        <option value="lundi" {{ old('jour_semaine') == 'lundi' ? 'selected' : '' }}>Lundi</option>
                                        <option value="mardi" {{ old('jour_semaine') == 'mardi' ? 'selected' : '' }}>Mardi</option>
                                        <option value="mercredi" {{ old('jour_semaine') == 'mercredi' ? 'selected' : '' }}>Mercredi</option>
                                        <option value="jeudi" {{ old('jour_semaine') == 'jeudi' ? 'selected' : '' }}>Jeudi</option>
                                        <option value="vendredi" {{ old('jour_semaine') == 'vendredi' ? 'selected' : '' }}>Vendredi</option>
                                        <option value="samedi" {{ old('jour_semaine') == 'samedi' ? 'selected' : '' }}>Samedi</option>
                                        <option value="dimanche" {{ old('jour_semaine') == 'dimanche' ? 'selected' : '' }}>Dimanche</option>
                                    </select>
                                    @error('jour_semaine')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="heure_debut" class="form-label small">Heure de début <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control form-control-sm @error('heure_debut') is-invalid @enderror" 
                                           id="heure_debut" name="heure_debut" value="{{ old('heure_debut') }}" required>
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="heure_fin" class="form-label small">Heure de fin <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control form-control-sm @error('heure_fin') is-invalid @enderror" 
                                           id="heure_fin" name="heure_fin" value="{{ old('heure_fin') }}" required>
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 4: Date début et Date fin -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="date_debut" class="form-label small">Date de début</label>
                                    <input type="date" class="form-control form-control-sm @error('date_debut') is-invalid @enderror" 
                                           id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="date_fin" class="form-label small">Date de fin</label>
                                    <input type="date" class="form-control form-control-sm @error('date_fin') is-invalid @enderror" 
                                           id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 5: Statut et Description -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="statut" class="form-label small">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('statut') is-invalid @enderror" 
                                            id="statut" name="statut" required>
                                        <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                        <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="notes" class="form-label small">Notes</label>
                                    <input type="text" class="form-control form-control-sm @error('notes') is-invalid @enderror" 
                                           id="notes" name="notes" value="{{ old('notes') }}" 
                                           placeholder="Notes additionnelles">
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 6: Description -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="description" class="form-label small">Description</label>
                                    <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="2" 
                                              placeholder="Description du calendrier de collecte">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i>Créer
                            </button>
                            <a href="{{ route('admin.calendrier.index') }}" class="btn btn-secondary btn-sm">
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