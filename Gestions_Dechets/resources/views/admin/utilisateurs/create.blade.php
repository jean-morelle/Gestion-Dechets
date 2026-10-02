@extends('layouts.app')

@section('title', 'Créer un compte agent')

@section('content')
<div class="page-header">
    <div>
        <h1>Créer un compte agent</h1>
        <p><a href="{{ route('admin.utilisateurs.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Utilisateurs</a></p>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <form method="POST" action="{{ route('admin.utilisateurs.store') }}" class="card">
            @csrf
            <div class="card-body">
                <fieldset class="mb-4">
                    <legend class="form-label fs-6">Type de compte</legend>
                    <div class="row g-3">
                        @foreach(['collecteur' => ['Collecteur', 'fa-truck', 'Réalise les tournées sur le terrain depuis son téléphone.'], 'admin' => ['Administrateur', 'fa-user-shield', 'Agent de la mairie : planifie, traite les demandes, gère les comptes.']] as $valeur => [$libelle, $icone, $desc])
                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="role" id="role-{{ $valeur }}" value="{{ $valeur }}" @checked(old('role', $role) === $valeur)>
                                <label class="theme-choix" for="role-{{ $valeur }}">
                                    <i class="fas {{ $icone }} text-primary" aria-hidden="true"></i>
                                    <strong>{{ $libelle }}</strong>
                                    <small>{{ $desc }}</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </fieldset>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="off">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="off">
                        <div class="form-text">Servira d’identifiant de connexion.</div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+228 90 12 34 56">
                        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="quartier" class="form-label">Secteur <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="text" class="form-control" id="quartier" name="quartier" value="{{ old('quartier') }}" list="liste-quartiers" autocomplete="off">
                        <datalist id="liste-quartiers">
                            @foreach(config('quartiers') as $q)<option value="{{ $q }}">@endforeach
                        </datalist>
                    </div>
                </div>

                <div class="alert alert-info small mt-4 mb-0 d-flex gap-2">
                    <i class="fas fa-key mt-1" aria-hidden="true"></i>
                    <div>Un mot de passe provisoire sera affiché une seule fois après la création. Transmettez-le à l’agent : il devra le remplacer à sa première connexion.</div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Créer le compte</button>
            </div>
        </form>
    </div>
</div>
@endsection
