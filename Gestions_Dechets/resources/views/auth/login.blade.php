@extends('layouts.auth')

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Logo et titre -->
        <div class="text-center mb-4">
            <h3 class="text-primary">Gestion Déchets Lomé</h3>
            <p class="text-muted">Connectez-vous à votre compte</p>
        </div>

        <!-- Messages d'erreur globaux -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Messages de succès -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Messages d'information -->
        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Se connecter
                </button>
            </div>
        </form>

        <!-- Séparateur -->
        <div class="text-center my-4">
            <hr class="my-3">
            <span class="text-muted bg-white px-3">ou</span>
        </div>

        <!-- Connexion Google -->
        <div class="d-grid">
            <a href="{{ route('auth.google') }}" class="btn btn-outline-danger">
                <i class="fab fa-google me-2"></i>
                Se connecter avec Google
            </a>
        </div>

        <div class="text-center mt-3">
            <p class="text-muted mb-0">Pas encore de compte ?</p>
            <a href="{{ route('register') }}" class="text-decoration-none">
                Créer un compte
            </a>
        </div>
    </div>
</div>
@endsection


















