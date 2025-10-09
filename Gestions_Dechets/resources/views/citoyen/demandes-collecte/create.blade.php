@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Nouvelle Demande</h6>
                </div>
                <div class="card-body py-3">
                    <form action="{{ route('citoyen.demandes-collecte.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="objet" class="form-label small">Objet de la demande <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('objet') is-invalid @enderror" 
                                           id="objet" name="objet" value="{{ old('objet') }}" required>
                                    @error('objet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="type_collecte" class="form-label small">Type de collecte <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('type_collecte') is-invalid @enderror" 
                                            id="type_collecte" name="type_collecte" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="menagere" {{ old('type_collecte') == 'menagere' ? 'selected' : '' }}>Ménagère</option>
                                        <option value="encombrant" {{ old('type_collecte') == 'encombrant' ? 'selected' : '' }}>Encombrants</option>
                                        <option value="vert" {{ old('type_collecte') == 'vert' ? 'selected' : '' }}>Vert</option>
                                        <option value="recyclable" {{ old('type_collecte') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                                        <option value="dangereux" {{ old('type_collecte') == 'dangereux' ? 'selected' : '' }}>Dangereux</option>
                                        <option value="demenagement" {{ old('type_collecte') == 'demenagement' ? 'selected' : '' }}>Déménagement</option>
                                    </select>
                                    @error('type_collecte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="urgence" class="form-label small">Urgence <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('urgence') is-invalid @enderror" 
                                            id="urgence" name="urgence" required>
                                        <option value="">Sélectionner l'urgence</option>
                                        <option value="faible" {{ old('urgence') == 'faible' ? 'selected' : '' }}>Faible</option>
                                        <option value="moyenne" {{ old('urgence') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="elevee" {{ old('urgence') == 'elevee' ? 'selected' : '' }}>Élevée</option>
                                        <option value="urgente" {{ old('urgence') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                    @error('urgence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                        <div class="mb-1">
                            <label for="description" class="form-label small">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label for="adresse" class="form-label small">Adresse <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="1" required>{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="date_souhaitee" class="form-label small">Date souhaitée</label>
                                    <input type="date" class="form-control form-control-sm @error('date_souhaitee') is-invalid @enderror" 
                                           id="date_souhaitee" name="date_souhaitee" value="{{ old('date_souhaitee') }}">
                                    @error('date_souhaitee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="heure_souhaitee" class="form-label small">Heure souhaitée</label>
                                    <input type="time" class="form-control form-control-sm @error('heure_souhaitee') is-invalid @enderror" 
                                           id="heure_souhaitee" name="heure_souhaitee" value="{{ old('heure_souhaitee') }}">
                                    @error('heure_souhaitee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                            <a href="{{ route('citoyen.demandes-collecte.index') }}" class="btn btn-secondary btn-sm">
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












