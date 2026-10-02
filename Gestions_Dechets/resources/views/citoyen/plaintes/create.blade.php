@extends('layouts.app')

@section('title', 'Nouvelle plainte')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Déposer une plainte</h6>
                </div>
                <div class="card-body py-3">
                    <form action="{{ route('citoyen.plaintes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="type_plainte" class="form-label small">Type de plainte <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('type_plainte') is-invalid @enderror" 
                                            id="type_plainte" name="type_plainte" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="collecte_retard" {{ old('type_plainte') === 'collecte_retard' ? 'selected' : '' }}>Retard dans la collecte</option>
                                        <option value="collecte_oubliee" {{ old('type_plainte') === 'collecte_oubliee' ? 'selected' : '' }}>Collecte oubliée</option>
                                        <option value="service_client" {{ old('type_plainte') === 'service_client' ? 'selected' : '' }}>Service client</option>
                                        <option value="autre" {{ old('type_plainte') === 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_plainte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="priorite" class="form-label small">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('priorite') is-invalid @enderror" 
                                            id="priorite" name="priorite" required>
                                        <option value="">Sélectionnez une priorité</option>
                                        <option value="faible" {{ old('priorite') === 'faible' ? 'selected' : '' }}>Faible</option>
                                        <option value="moyenne" {{ old('priorite') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="elevee" {{ old('priorite') === 'elevee' ? 'selected' : '' }}>Élevée</option>
                                        <option value="urgente" {{ old('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                    @error('priorite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label for="sujet" class="form-label small">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('sujet') is-invalid @enderror" 
                                   id="sujet" name="sujet" value="{{ old('sujet') }}" 
                                   placeholder="Résumé de votre plainte" required>
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label for="description" class="form-label small">Description détaillée <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2" 
                                      placeholder="Décrivez en détail le problème rencontré..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-carte.choix :latitude="old('latitude')" :longitude="old('longitude')"
                                       label="Emplacement sur la carte (facultatif)"
                                       aide="Si le problème concerne un lieu précis, cliquez dessus sur la carte." />

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-1">
                                    <label for="adresse" class="form-label small">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('adresse') is-invalid @enderror" 
                                           id="adresse" name="adresse" value="{{ old('adresse') }}" 
                                           placeholder="Adresse où le problème s'est produit" required>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="quartier" class="form-label small">Quartier <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('quartier') is-invalid @enderror" 
                                           id="quartier" name="quartier" value="{{ old('quartier') }}" 
                                           placeholder="Quartier concerné" required>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="contact_telephone" class="form-label small">Téléphone</label>
                                    <input type="tel" class="form-control form-control-sm @error('contact_telephone') is-invalid @enderror" 
                                           id="contact_telephone" name="contact_telephone" value="{{ old('contact_telephone') }}" 
                                           placeholder="Votre numéro">
                                    @error('contact_telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="photo" class="form-label small">Photo</label>
                                    <input type="file" class="form-control form-control-sm @error('photo') is-invalid @enderror" 
                                           id="photo" name="photo" accept="image/*">
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                            <a href="{{ route('citoyen.plaintes.index') }}" class="btn btn-secondary btn-sm">
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














