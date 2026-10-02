@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="auth-form">
    <img src="{{ asset('images/collectplus-logo.png') }}" class="auth-logo d-lg-none" alt="CollectPlus — Gestion intelligente des déchets, Togo">

    <h1>Mot de passe oublié</h1>
    <p class="text-body-secondary mb-4">Indiquez l’adresse e-mail de votre compte : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                   value="{{ old('email') }}" placeholder="nom@exemple.com" autocomplete="email" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Recevoir le lien</button>
    </form>

    <p class="small text-body-secondary mt-4">
        Agent de la mairie ou collecteur sans accès à votre e-mail ? Demandez à un administrateur de vous générer un mot de passe provisoire.
    </p>

    <p class="text-center mt-3 mb-0">
        <a href="{{ route('login') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Retour à la connexion</a>
    </p>
</div>
@endsection
