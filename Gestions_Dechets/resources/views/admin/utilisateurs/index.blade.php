@extends('layouts.app')

@section('title', 'Utilisateurs')

@php
    $roles = \App\Http\Controllers\AdminUtilisateurController::ROLES;
    $statuts = \App\Http\Controllers\AdminUtilisateurController::STATUTS;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Utilisateurs</h1>
        <p>
            {{ $compteurs['citoyen'] ?? 0 }} citoyen{{ ($compteurs['citoyen'] ?? 0) > 1 ? 's' : '' }} ·
            {{ $compteurs['collecteur'] ?? 0 }} collecteur{{ ($compteurs['collecteur'] ?? 0) > 1 ? 's' : '' }} ·
            {{ $compteurs['admin'] ?? 0 }} administrateur{{ ($compteurs['admin'] ?? 0) > 1 ? 's' : '' }}
        </p>
    </div>
    <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-1" aria-hidden="true"></i>Créer un compte agent
    </a>
</div>

<form method="GET" class="row g-2 mb-3" role="search">
    <div class="col-md-5">
        <label for="q" class="visually-hidden">Rechercher</label>
        <input type="search" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Nom, e-mail, téléphone ou quartier…">
    </div>
    <div class="col-6 col-md-3">
        <label for="filtre-role" class="visually-hidden">Rôle</label>
        <select class="form-select" id="filtre-role" name="role" onchange="this.form.submit()">
            <option value="">Tous les rôles</option>
            @foreach($roles as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected(request('role') === $valeur)>{{ $libelle }}s</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label for="filtre-statut" class="visually-hidden">Statut</label>
        <select class="form-select" id="filtre-statut" name="statut" onchange="this.form.submit()">
            <option value="">Tous</option>
            @foreach($statuts as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
    </div>
</form>

<div class="card">
    @if($utilisateurs->isEmpty())
        <div class="empty-state">
            <i class="fas fa-users" aria-hidden="true"></i>
            <p>Aucun compte ne correspond à ces critères.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Rôle</th>
                        <th>Quartier</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($utilisateurs as $u)
                        <tr>
                            <td class="min-w-0">
                                <div class="d-flex align-items-center gap-2">
                                    <x-avatar :user="$u" :taille="32" />
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.utilisateurs.show', $u) }}" class="fw-medium">{{ $u->name }}</a>
                                        <div class="small text-body-secondary text-truncate">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $roles[$u->role] ?? $u->role }}</td>
                            <td>{{ $u->quartier ?: '—' }}</td>
                            <td><span class="badge {{ $u->statut === 'actif' ? 'tone-green' : ($u->statut === 'suspendu' ? 'tone-amber' : 'tone-slate') }}">{{ $statuts[$u->statut] ?? $u->statut }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $utilisateurs->links() }}</div>
@endsection
