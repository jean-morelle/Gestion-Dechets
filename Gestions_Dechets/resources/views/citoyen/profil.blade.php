@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </h4>
                <p class="text-muted">Gérez vos informations personnelles et vos préférences.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quartier" class="form-label">Quartier</label>
                                    <select class="form-select @error('quartier') is-invalid @enderror" 
                                            id="quartier" name="quartier">
                                        <option value="">Sélectionner un quartier</option>
                                        <option value="Centre-ville" {{ old('quartier', $user->quartier) == 'Centre-ville' ? 'selected' : '' }}>Centre-ville</option>
                                        <option value="Quartier Nord" {{ old('quartier', $user->quartier) == 'Quartier Nord' ? 'selected' : '' }}>Quartier Nord</option>
                                        <option value="Quartier Sud" {{ old('quartier', $user->quartier) == 'Quartier Sud' ? 'selected' : '' }}>Quartier Sud</option>
                                        <option value="Quartier Est" {{ old('quartier', $user->quartier) == 'Quartier Est' ? 'selected' : '' }}>Quartier Est</option>
                                        <option value="Quartier Ouest" {{ old('quartier', $user->quartier) == 'Quartier Ouest' ? 'selected' : '' }}>Quartier Ouest</option>
                                        <option value="Zone Industrielle" {{ old('quartier', $user->quartier) == 'Zone Industrielle' ? 'selected' : '' }}>Zone Industrielle</option>
                                        <option value="Résidentiel Alpha" {{ old('quartier', $user->quartier) == 'Résidentiel Alpha' ? 'selected' : '' }}>Résidentiel Alpha</option>
                                        <option value="Résidentiel Beta" {{ old('quartier', $user->quartier) == 'Résidentiel Beta' ? 'selected' : '' }}>Résidentiel Beta</option>
                                    </select>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse complète</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" 
                                      placeholder="Votre adresse complète">{{ old('adresse', $user->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo de profil</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->photo)
                                <div class="mt-2">
                                    <small class="text-muted">Photo actuelle :</small>
                                    <img src="{{ Storage::url($user->photo) }}" alt="Photo de profil" 
                                         class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations du compte -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations du Compte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rôle</label>
                        <p class="mb-0">
                            <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Membre depuis</label>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </h4>
                <p class="text-muted">Gérez vos informations personnelles et vos préférences.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quartier" class="form-label">Quartier</label>
                                    <select class="form-select @error('quartier') is-invalid @enderror" 
                                            id="quartier" name="quartier">
                                        <option value="">Sélectionner un quartier</option>
                                        <option value="Centre-ville" {{ old('quartier', $user->quartier) == 'Centre-ville' ? 'selected' : '' }}>Centre-ville</option>
                                        <option value="Quartier Nord" {{ old('quartier', $user->quartier) == 'Quartier Nord' ? 'selected' : '' }}>Quartier Nord</option>
                                        <option value="Quartier Sud" {{ old('quartier', $user->quartier) == 'Quartier Sud' ? 'selected' : '' }}>Quartier Sud</option>
                                        <option value="Quartier Est" {{ old('quartier', $user->quartier) == 'Quartier Est' ? 'selected' : '' }}>Quartier Est</option>
                                        <option value="Quartier Ouest" {{ old('quartier', $user->quartier) == 'Quartier Ouest' ? 'selected' : '' }}>Quartier Ouest</option>
                                        <option value="Zone Industrielle" {{ old('quartier', $user->quartier) == 'Zone Industrielle' ? 'selected' : '' }}>Zone Industrielle</option>
                                        <option value="Résidentiel Alpha" {{ old('quartier', $user->quartier) == 'Résidentiel Alpha' ? 'selected' : '' }}>Résidentiel Alpha</option>
                                        <option value="Résidentiel Beta" {{ old('quartier', $user->quartier) == 'Résidentiel Beta' ? 'selected' : '' }}>Résidentiel Beta</option>
                                    </select>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse complète</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="3" 
                                      placeholder="Votre adresse complète">{{ old('adresse', $user->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo de profil</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->photo)
                                <div class="mt-2">
                                    <small class="text-muted">Photo actuelle :</small>
                                    <img src="{{ Storage::url($user->photo) }}" alt="Photo de profil" 
                                         class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations du compte -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations du Compte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rôle</label>
                        <p class="mb-0">
                            <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Membre depuis</label>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
