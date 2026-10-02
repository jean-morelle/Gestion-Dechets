@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="auth-form">
    <img src="{{ asset('images/collectplus-logo.png') }}" class="auth-logo d-lg-none" alt="CollectPlus — Gestion intelligente des déchets, Togo">

    <h1>Connexion</h1>
    <p class="text-body-secondary mb-4">Accédez à votre espace personnel.</p>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info" role="status">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="nom@exemple.com"
                   autocomplete="email"
                   required
                   autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-baseline">
                <label for="password" class="form-label">Mot de passe</label>
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Mot de passe oublié ?</a>
            </div>
            <div class="password-field">
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       autocomplete="current-password"
                       required>
                <button type="button" class="toggle-password" data-target="password" aria-label="Afficher le mot de passe">
                    <i class="far fa-eye" aria-hidden="true"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Rester connecté</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Se connecter</button>
    </form>

    <div class="auth-divider">ou</div>

    <a href="{{ route('auth.google') }}" class="btn btn-google w-100 py-2">
        <i class="fab fa-google me-2" aria-hidden="true"></i>Continuer avec Google
    </a>

    <p class="text-center text-body-secondary mt-4 mb-0">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Créer un compte</a>
    </p>
</div>
@endsection
