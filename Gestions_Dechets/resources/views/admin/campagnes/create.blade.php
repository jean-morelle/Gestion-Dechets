@extends('layouts.app')

@section('title', 'Nouvelle campagne')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Nouvelle campagne</h3>
            <p>Créer une campagne de sensibilisation</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.campagnes.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror" 
                                           id="titre" name="titre" value="{{ old('titre') }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Choisir un type</option>
                                        <option value="affiche" {{ old('type') == 'affiche' ? 'selected' : '' }}>Affiche</option>
                                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Vidéo</option>
                                        <option value="message" {{ old('type') == 'message' ? 'selected' : '' }}>Message</option>
                                        <option value="infographie" {{ old('type') == 'infographie' ? 'selected' : '' }}>Infographie</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" 
                                           id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" 
                                           id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="fichier-section" style="display: none;">
                            <label for="fichier" class="form-label">Fichier</label>
                            <input type="file" class="form-control @error('fichier') is-invalid @enderror" 
                                   id="fichier" name="fichier" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            @error('fichier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="video-section" style="display: none;">
                            <label for="url_video" class="form-label">URL vidéo</label>
                            <input type="url" class="form-control @error('url_video') is-invalid @enderror" 
                                   id="url_video" name="url_video" value="{{ old('url_video') }}">
                            @error('url_video')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="message-section" style="display: none;">
                            <label for="contenu_message" class="form-label">Contenu du message</label>
                            <textarea class="form-control @error('contenu_message') is-invalid @enderror" 
                                      id="contenu_message" name="contenu_message" rows="3">{{ old('contenu_message') }}</textarea>
                            @error('contenu_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image_preview" class="form-label">Image de prévisualisation</label>
                            <input type="file" class="form-control @error('image_preview') is-invalid @enderror" 
                                   id="image_preview" name="image_preview" accept="image/*">
                            @error('image_preview')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="publier" value="1" class="btn btn-success">Publier</button>
                            <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            <a href="{{ route('admin.campagnes.index') }}" class="btn btn-secondary">Annuler</a>
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
    
    document.getElementById('fichier-section').style.display = 'none';
    document.getElementById('video-section').style.display = 'none';
    document.getElementById('message-section').style.display = 'none';
    
    if (type === 'affiche' || type === 'infographie') {
        document.getElementById('fichier-section').style.display = 'block';
    } else if (type === 'video') {
        document.getElementById('video-section').style.display = 'block';
    } else if (type === 'message') {
        document.getElementById('message-section').style.display = 'block';
    }
});
</script>
@endsection