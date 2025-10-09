@extends('layouts.app')

@section('title', 'Modifier la Campagne')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Modifier la Campagne : {{ $campagne->titre }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.campagnes.update', $campagne) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Informations générales</h6>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label fw-bold">Titre de la campagne <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror" 
                                           id="titre" name="titre" value="{{ old('titre', $campagne->titre) }}" 
                                           placeholder="Ex: Campagne contre les dépôts sauvages" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label fw-bold">Type de campagne <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="affiche" {{ old('type', $campagne->type) == 'affiche' ? 'selected' : '' }}>Affiche numérique</option>
                                        <option value="video" {{ old('type', $campagne->type) == 'video' ? 'selected' : '' }}>Vidéo</option>
                                        <option value="message" {{ old('type', $campagne->type) == 'message' ? 'selected' : '' }}>Message</option>
                                        <option value="infographie" {{ old('type', $campagne->type) == 'infographie' ? 'selected' : '' }}>Infographie</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Décrivez l'objectif et le contenu de cette campagne" required>{{ old('description', $campagne->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contenu selon le type -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Contenu de la campagne</h6>
                            </div>
                            
                            <!-- Fichier pour affiche/infographie -->
                            <div class="col-md-6" id="fichier-section" style="display: {{ in_array($campagne->type, ['affiche', 'infographie']) ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="fichier" class="form-label fw-bold">Fichier</label>
                                    <input type="file" class="form-control @error('fichier') is-invalid @enderror" 
                                           id="fichier" name="fichier" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <div class="form-text">Formats acceptés: JPG, PNG, PDF, DOC, DOCX (max 10MB)</div>
                                    @if($campagne->fichier)
                                        <div class="mt-2">
                                            <small class="text-muted">Fichier actuel :</small>
                                            <a href="{{ asset('storage/' . $campagne->fichier) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye me-1"></i>Voir
                                            </a>
                                        </div>
                                    @endif
                                    @error('fichier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- URL vidéo -->
                            <div class="col-md-6" id="video-section" style="display: {{ $campagne->type === 'video' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="url_video" class="form-label fw-bold">URL de la vidéo</label>
                                    <input type="url" class="form-control @error('url_video') is-invalid @enderror" 
                                           id="url_video" name="url_video" value="{{ old('url_video', $campagne->url_video) }}" 
                                           placeholder="https://youtube.com/watch?v=...">
                                    @error('url_video')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contenu message -->
                            <div class="col-12" id="message-section" style="display: {{ $campagne->type === 'message' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="contenu_message" class="form-label fw-bold">Contenu du message</label>
                                    <textarea class="form-control @error('contenu_message') is-invalid @enderror" 
                                              id="contenu_message" name="contenu_message" rows="4" 
                                              placeholder="Tapez le message de sensibilisation...">{{ old('contenu_message', $campagne->contenu_message) }}</textarea>
                                    @error('contenu_message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Image de prévisualisation -->
                        <div class="mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Prévisualisation</h6>
                            </div>
                            <div class="mb-3">
                                <label for="image_preview" class="form-label fw-bold">Image de prévisualisation</label>
                                <input type="file" class="form-control @error('image_preview') is-invalid @enderror" 
                                       id="image_preview" name="image_preview" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Image qui apparaîtra dans la liste des campagnes (JPG, PNG)</div>
                                @if($campagne->image_preview)
                                    <div class="mt-2">
                                        <small class="text-muted">Image actuelle :</small>
                                        <a href="{{ asset('storage/' . $campagne->image_preview) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                    </div>
                                @endif
                                @error('image_preview')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>

                        <!-- Période de diffusion -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Période de diffusion</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label fw-bold">Date de début</label>
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" 
                                           id="date_debut" name="date_debut" value="{{ old('date_debut', $campagne->date_debut?->format('Y-m-d')) }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label fw-bold">Date de fin</label>
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" 
                                           id="date_fin" name="date_fin" value="{{ old('date_fin', $campagne->date_fin?->format('Y-m-d')) }}">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Quartiers cibles -->
                        <div class="mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Quartiers cibles (optionnel)</h6>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sélectionner les quartiers</label>
                                <div class="row">
                                    @php
                                        $quartiers = ['Centre-ville', 'Tokoin', 'Bè', 'Agoè', 'Lomé II', 'Lomé III', 'Lomé IV', 'Lomé V', 'Lomé VI'];
                                        $quartiersSelectionnes = old('quartiers_cibles', $campagne->quartiers_cibles ?? []);
                                    @endphp
                                    @foreach($quartiers as $quartier)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="quartiers_cibles[]" value="{{ $quartier }}" 
                                                       id="quartier_{{ $loop->index }}"
                                                       {{ in_array($quartier, $quartiersSelectionnes) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="quartier_{{ $loop->index }}">
                                                    {{ $quartier }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Si aucun quartier n'est sélectionné, la campagne cible tous les quartiers</div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex gap-2">
                            <button type="submit" name="publier" value="1" class="btn btn-success">
                                <i class="fas fa-play me-1"></i>Publier la campagne
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Sauvegarder
                            </button>
                            <a href="{{ route('admin.campagnes.show', $campagne) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    
    // Masquer toutes les sections
    document.getElementById('fichier-section').style.display = 'none';
    document.getElementById('video-section').style.display = 'none';
    document.getElementById('message-section').style.display = 'none';
    
    // Afficher la section appropriée
    if (type === 'affiche' || type === 'infographie') {
        document.getElementById('fichier-section').style.display = 'block';
    } else if (type === 'video') {
        document.getElementById('video-section').style.display = 'block';
    } else if (type === 'message') {
        document.getElementById('message-section').style.display = 'block';
    }
});

// Validation des dates
document.getElementById('date_debut').addEventListener('change', function() {
    const dateFin = document.getElementById('date_fin');
    if (this.value && dateFin.value && this.value > dateFin.value) {
        dateFin.value = this.value;
    }
    dateFin.min = this.value;
});

document.getElementById('date_fin').addEventListener('change', function() {
    const dateDebut = document.getElementById('date_debut');
    if (this.value && dateDebut.value && this.value < dateDebut.value) {
        this.value = dateDebut.value;
    }
});
</script>
@endsection















