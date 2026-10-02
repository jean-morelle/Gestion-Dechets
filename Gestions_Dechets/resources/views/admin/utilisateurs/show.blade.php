@extends('layouts.app')

@section('title', $utilisateur->name)

@php
    $roles = \App\Http\Controllers\AdminUtilisateurController::ROLES;
    $statuts = \App\Http\Controllers\AdminUtilisateurController::STATUTS;
@endphp

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <x-avatar :user="$utilisateur" :taille="56" />
        <div>
            <h1>{{ $utilisateur->name }}</h1>
            <p class="mb-0">
                {{ $roles[$utilisateur->role] ?? $utilisateur->role }} ·
                <span class="badge {{ $utilisateur->statut === 'actif' ? 'tone-green' : ($utilisateur->statut === 'suspendu' ? 'tone-amber' : 'tone-slate') }}">{{ $statuts[$utilisateur->statut] ?? $utilisateur->statut }}</span>
                @if($utilisateur->doit_changer_mot_de_passe)<span class="badge tone-blue">Première connexion en attente</span>@endif
            </p>
        </div>
    </div>
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Utilisateurs</a>
</div>

@if(session('mot_de_passe_provisoire'))
    <div class="alert alert-warning" role="status">
        <div class="fw-semibold mb-1">Mot de passe provisoire de {{ $utilisateur->name }}</div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <code class="fs-5 user-select-all px-2 py-1 bg-body rounded" id="mdp-provisoire">{{ session('mot_de_passe_provisoire') }}</code>
            <button type="button" class="btn btn-sm btn-outline-dark" data-copier="#mdp-provisoire">Copier</button>
        </div>
        <div class="small mt-2">
            Notez-le maintenant : il ne sera plus affiché. Identifiant : <strong>{{ $utilisateur->email }}</strong>.
            L’agent devra choisir son propre mot de passe à la première connexion.
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">E-mail</dt>
                    <dd class="col-sm-8"><a href="mailto:{{ $utilisateur->email }}">{{ $utilisateur->email }}</a></dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Téléphone</dt>
                    <dd class="col-sm-8">
                        @if($utilisateur->telephone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $utilisateur->telephone) }}">{{ $utilisateur->telephone }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Quartier</dt>
                    <dd class="col-sm-8">{{ $utilisateur->quartier ?: '—' }}</dd>
                    @if($utilisateur->adresse)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Adresse</dt>
                        <dd class="col-sm-8">{{ $utilisateur->adresse }}</dd>
                    @endif
                    <dt class="col-sm-4 fw-normal text-body-secondary">Inscrit le</dt>
                    <dd class="col-sm-8 mb-0">{{ $utilisateur->created_at?->translatedFormat('j F Y') }}@if($utilisateur->google_id) · via Google @endif</dd>
                </dl>
            </div>
            @if($activite)
                <div class="profil-activite" style="grid-template-columns: repeat({{ count($activite) }}, 1fr)">
                    @foreach($activite as $libelle => $nombre)
                        <div>
                            <div class="fs-5 fw-semibold">{{ $nombre }}</div>
                            <div class="small text-body-secondary">{{ $libelle }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="col-xl-5">
        @if($estMoi)
            <div class="card">
                <div class="card-body small text-body-secondary">
                    C’est votre compte. Modifiez vos informations depuis <a href="{{ route('profile.edit') }}">Mon profil</a> et votre mot de passe depuis <a href="{{ route('settings.index') }}#securite">Paramètres</a>.
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}" class="card mb-4">
                @csrf
                @method('PUT')
                <div class="card-header"><h5>Accès</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select" id="role" name="role">
                                @foreach($roles as $valeur => $libelle)
                                    <option value="{{ $valeur }}" @selected($utilisateur->role === $valeur)>{{ $libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                @foreach($statuts as $valeur => $libelle)
                                    <option value="{{ $valeur }}" @selected($utilisateur->statut === $valeur)>{{ $libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-text">Un compte suspendu ou désactivé ne peut plus se connecter.</div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>

            @if(! $utilisateur->google_id)
                <form method="POST" action="{{ route('admin.utilisateurs.reinitialiser', $utilisateur) }}" class="card mb-4"
                      onsubmit="return confirm('Générer un nouveau mot de passe provisoire ? L’ancien ne fonctionnera plus.')">
                    @csrf
                    <div class="card-body">
                        <h2 class="fs-6 fw-semibold">Mot de passe oublié ?</h2>
                        <p class="small text-body-secondary">Générez un mot de passe provisoire à transmettre à la personne. Elle devra le changer à sa prochaine connexion.</p>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Générer un mot de passe provisoire</button>
                    </div>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.utilisateurs.destroy', $utilisateur) }}" class="card"
                  onsubmit="return confirm('Supprimer ce compte ?')">
                @csrf
                @method('DELETE')
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Supprimer le compte</h2>
                    <p class="small text-body-secondary">Si le compte a déjà une activité, il sera désactivé pour conserver l’historique.</p>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copier]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var texte = document.querySelector(btn.dataset.copier).textContent.trim();
            navigator.clipboard.writeText(texte).then(function () {
                btn.textContent = 'Copié';
                setTimeout(function () { btn.textContent = 'Copier'; }, 2000);
            });
        });
    });
</script>
@endpush
