@extends('layouts.auth')

@section('title', 'Créer un compte')

@php
    $quartiers = config('quartiers');
@endphp

@section('content')
<div class="auth-form wide">
    <img src="{{ asset('images/collectplus-logo.png') }}" class="auth-logo d-lg-none" alt="CollectPlus — Gestion intelligente des déchets, Togo">

    <h1>Créer un compte citoyen</h1>
    <p class="text-body-secondary mb-4">Quelques informations suffisent pour commencer à signaler et suivre vos demandes.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nom complet</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nom@exemple.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="password-field">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required autocomplete="new-password" aria-describedby="passwordHelp">
                    <button type="button" class="toggle-password" data-target="password" aria-label="Afficher le mot de passe">
                        <i class="far fa-eye" aria-hidden="true"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="passwordHelp" class="form-text">8 caractères minimum.</div>
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirmation</label>
                <div class="password-field">
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Afficher le mot de passe">
                        <i class="far fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                       id="telephone" name="telephone" value="{{ old('telephone') }}" autocomplete="tel" placeholder="90 00 00 00">
                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="quartier" class="form-label">Quartier</label>
                <input type="text" class="form-control @error('quartier') is-invalid @enderror" id="quartier" name="quartier" value="{{ old('quartier') }}" list="liste-quartiers" placeholder="Ex. : Tokoin" required autocomplete="off">
                <datalist id="liste-quartiers">
                    @foreach($quartiers as $q)
                        <option value="{{ $q }}">
                    @endforeach
                </datalist>
                @error('quartier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="adresse" class="form-label">Adresse <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                <input type="text" class="form-control @error('adresse') is-invalid @enderror"
                       id="adresse" name="adresse" value="{{ old('adresse') }}" autocomplete="street-address" placeholder="Rue, repère…">
                @error('adresse')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" required @checked(old('terms'))>
                    <label class="form-check-label" for="terms">
                        J’accepte les <a href="{{ route('legal.conditions') }}" target="_blank">conditions d’utilisation</a>
                        et j’ai lu la <a href="{{ route('legal.confidentialite') }}" target="_blank">politique de confidentialité</a>.
                    </label>
                    @error('terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100 py-2">Créer mon compte</button>
            </div>
        </div>
    </form>

    <div class="auth-divider">ou</div>

    <a href="{{ route('auth.google') }}" class="btn btn-google w-100 py-2">
        <i class="fab fa-google me-2" aria-hidden="true"></i>S'inscrire avec Google
    </a>

    <p class="text-center text-body-secondary mt-4 mb-0">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Se connecter</a>
    </p>
</div>
@endsection
