@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Créer un compte</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone') }}" autocomplete="tel">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quartier" class="form-label">Quartier <span class="text-danger">*</span></label>
                                    <select class="form-select @error('quartier') is-invalid @enderror" id="quartier" name="quartier" required>
                                        <option value="">Choisissez votre quartier</option>
                                        <option value="Centre-ville" {{ old('quartier') == 'Centre-ville' ? 'selected' : '' }}>Centre-ville</option>
                                        <option value="Nyékonakpoè" {{ old('quartier') == 'Nyékonakpoè' ? 'selected' : '' }}>Nyékonakpoè</option>
                                        <option value="Tokoin" {{ old('quartier') == 'Tokoin' ? 'selected' : '' }}>Tokoin</option>
                                        <option value="Bè" {{ old('quartier') == 'Bè' ? 'selected' : '' }}>Bè</option>
                                        <option value="Adidogomé" {{ old('quartier') == 'Adidogomé' ? 'selected' : '' }}>Adidogomé</option>
                                        <option value="Agoè" {{ old('quartier') == 'Agoè' ? 'selected' : '' }}>Agoè</option>
                                        <option value="Attiegou" {{ old('quartier') == 'Attiegou' ? 'selected' : '' }}>Attiegou</option>
                                        <option value="Quartier Nord" {{ old('quartier') == 'Quartier Nord' ? 'selected' : '' }}>Quartier Nord</option>
                                        <option value="Quartier Sud" {{ old('quartier') == 'Quartier Sud' ? 'selected' : '' }}>Quartier Sud</option>
                                        <option value="Quartier Est" {{ old('quartier') == 'Quartier Est' ? 'selected' : '' }}>Quartier Est</option>
                                    </select>
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('adresse') is-invalid @enderror" 
                                   id="adresse" name="adresse" value="{{ old('adresse') }}" autocomplete="street-address">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a> et la <a href="#" class="text-decoration-none">politique de confidentialité</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Créer mon compte
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-muted mb-0">Déjà un compte ?</p>
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection