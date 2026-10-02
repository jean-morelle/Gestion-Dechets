@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
<div class="auth-form">
    <img src="{{ asset('images/collectplus-logo.png') }}" class="auth-logo d-lg-none" alt="CollectPlus — Gestion intelligente des déchets, Togo">

    <h1>Nouveau mot de passe</h1>
    <p class="text-body-secondary mb-4">Choisissez un mot de passe d’au moins 8 caractères.</p>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                   value="{{ old('email', $email) }}" autocomplete="email" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <div class="password-field">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                       autocomplete="new-password" minlength="8" required autofocus>
                <button type="button" class="toggle-password" data-target="password" aria-label="Afficher le mot de passe">
                    <i class="far fa-eye" aria-hidden="true"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <div class="password-field">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Afficher le mot de passe">
                    <i class="far fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Enregistrer le mot de passe</button>
    </form>
</div>
@endsection
