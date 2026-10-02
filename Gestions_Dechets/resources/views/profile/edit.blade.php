@extends('layouts.app')

@section('title', 'Mon profil')

@php
    $roles = ['citoyen' => 'Citoyen', 'collecteur' => 'Collecteur', 'admin' => 'Administrateur'];
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Mon profil</h1>
        <p>Les équipes de collecte utilisent ces informations pour vous joindre et retrouver vos demandes.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card profil-identite">
            <div class="card-body text-center">
                <x-avatar :user="$user" :taille="88" class="mb-3" />
                <h2 class="fs-5 mb-0">{{ $user->name }}</h2>
                <div class="small text-body-secondary text-break">{{ $user->email }}</div>
                <span class="badge tone-green mt-2">{{ $roles[$user->role] ?? ucfirst($user->role) }}</span>
            </div>

            @if($activite)
                <div class="profil-activite">
                    @foreach($activite as $libelle => $nombre)
                        <div>
                            <div class="fs-5 fw-semibold">{{ $nombre }}</div>
                            <div class="small text-body-secondary">{{ $libelle }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <ul class="list-unstyled small mb-0 profil-details">
                <li><i class="fas fa-location-dot" aria-hidden="true"></i>{{ $user->quartier ?: 'Quartier non renseigné' }}</li>
                <li><i class="far fa-calendar" aria-hidden="true"></i>Inscrit depuis {{ $user->created_at?->translatedFormat('F Y') }}</li>
                @if($user->google_id)
                    <li><i class="fab fa-google" aria-hidden="true"></i>Connexion avec Google</li>
                @endif
            </ul>
        </div>

        <a href="{{ route('settings.index') }}#securite" class="quick-action h-auto mt-3">
            <span class="stat-icon tone-slate"><i class="fas fa-lock" aria-hidden="true"></i></span>
            <span>
                <strong>Mot de passe et sécurité</strong>
                <small>Modifier le mot de passe, voir les appareils connectés</small>
            </span>
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </a>
    </div>

    <div class="col-lg-8">
        @if($aCompleter->isNotEmpty())
            <div class="alert alert-warning d-flex gap-2 small" role="status">
                <i class="fas fa-circle-info mt-1" aria-hidden="true"></i>
                <div>
                    Ajoutez {{ $aCompleter->values()->join(', ', ' et ') }} : le collecteur pourra vous contacter
                    et trouver plus facilement le lieu de vos demandes.
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>Informations personnelles</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="form-profil">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div id="apercu-photo">
                            <x-avatar :user="$user" :taille="64" />
                        </div>
                        <div>
                            {{-- Champ masqué : le libellé fait office de bouton (le focus clavier reste visible, voir app.css) --}}
                            <input type="file" class="visually-hidden champ-photo" id="photo" name="photo" accept="image/*">
                            <div class="d-flex flex-wrap gap-2">
                                <label for="photo" class="btn btn-sm btn-outline-primary mb-0">
                                    {{ $user->photo ? 'Changer la photo' : 'Ajouter une photo' }}
                                </label>
                                @if($user->photo)
                                    <button type="submit" form="form-retirer-photo" class="btn btn-sm btn-link text-danger" data-no-lock>Retirer</button>
                                @endif
                            </div>
                            <div class="form-text mt-1" id="photo-aide">JPG, PNG ou WebP. Les photos lourdes sont réduites automatiquement.</div>
                            @error('photo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" placeholder="+228 90 12 34 56" autocomplete="tel" inputmode="tel">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="quartier" class="form-label">Quartier</label>
                            <input type="text" class="form-control @error('quartier') is-invalid @enderror" id="quartier" name="quartier" value="{{ old('quartier', $user->quartier) }}" list="liste-quartiers" placeholder="Ex. : Tokoin" autocomplete="off">
                            <datalist id="liste-quartiers">
                                @foreach(config('quartiers') as $q)
                                    <option value="{{ $q }}">
                                @endforeach
                            </datalist>
                            @error('quartier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse', $user->adresse) }}" placeholder="Rue, maison, repère proche (ex. : derrière la pharmacie)" autocomplete="street-address">
                            <div class="form-text">Proposée par défaut quand vous faites un signalement.</div>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>

                @if($user->photo)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}" id="form-retirer-photo">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Aperçu immédiat de la photo choisie, avant l'enregistrement
    document.getElementById('photo').addEventListener('change', function () {
        var fichier = this.files[0];
        if (!fichier) return;
        var img = document.createElement('img');
        img.className = 'avatar object-fit-cover';
        img.style.cssText = 'width:64px;height:64px';
        img.alt = '';
        img.src = URL.createObjectURL(fichier);
        document.getElementById('apercu-photo').replaceChildren(img);
        document.getElementById('photo-aide').textContent = fichier.name + ' — cliquez sur « Enregistrer » pour valider.';
    });
</script>
@endpush
