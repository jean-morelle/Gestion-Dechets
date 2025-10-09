@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Utilisateurs</h6>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Tous les rôles</option>
                        <option>Citoyens</option>
                        <option>Collecteurs</option>
                        <option>Administrateurs</option>
                    </select>
                </div>
                <div class="card-body py-3">
                    @if(isset($users) && count($users) > 0)
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Quartier</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ Str::limit($user->name, 25) }}</td>
                                <td>{{ Str::limit($user->email, 30) }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'collecteur' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->quartier ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->statut == 'actif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->statut) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun utilisateur trouvé</p>
                    </div>
                    @endif

                    @if(isset($users) && $users->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $users->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection