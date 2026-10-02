@extends('layouts.app')

@section('title', 'Nouveau signalement')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Nouveau Signalement</h6>
                </div>
                <div class="card-body py-3">
                    <form method="POST" action="{{ route('citoyen.signalements.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="type_dechet" class="form-label small">Type de déchet <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('type_dechet') is-invalid @enderror" id="type_dechet" name="type_dechet" required>
                                        <option value="">Sélectionner le type de déchet</option>
                                        <option value="dechet_menager" {{ old('type_dechet') === 'dechet_menager' ? 'selected' : '' }}>Ménagère</option>
                                        <option value="dechet_vert" {{ old('type_dechet') === 'dechet_vert' ? 'selected' : '' }}>Déchets verts</option>
                                        <option value="encombrant" {{ old('type_dechet') === 'encombrant' ? 'selected' : '' }}>Encombrants</option>
                                        <option value="dechet_dangereux" {{ old('type_dechet') === 'dechet_dangereux' ? 'selected' : '' }}>Déchets dangereux/électroniques</option>
                                        <option value="dechet_recyclable" {{ old('type_dechet') === 'dechet_recyclable' ? 'selected' : '' }}>Recyclable</option>
                                        <option value="autre" {{ old('type_dechet') === 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_dechet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="priorite" class="form-label small">Niveau d'urgence <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                                        <option value="">---</option>
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
                            <label for="description" class="form-label small">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="1" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-carte.choix :latitude="old('latitude')" :longitude="old('longitude')" requis
                                       aide="Cliquez à l’endroit des déchets, ou utilisez « Ma position » si vous y êtes. L’adresse se remplit toute seule." />

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-1">
                                    <label for="adresse" class="form-label small">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('adresse') is-invalid @enderror" 
                                           id="adresse" name="adresse" value="{{ old('adresse', auth()->user()->adresse) }}" required>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label for="quartier" class="form-label small">Quartier <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('quartier') is-invalid @enderror" 
                                           id="quartier" name="quartier" value="{{ old('quartier', auth()->user()->quartier) }}" required>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
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
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                            <a href="{{ route('citoyen.signalements.index') }}" class="btn btn-secondary btn-sm">
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
















