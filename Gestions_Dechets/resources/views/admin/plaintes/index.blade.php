@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Plaintes</h6>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Statut: Tous</option>
                        <option>En attente</option>
                        <option>En cours</option>
                        <option>Traitées</option>
                    </select>
                </div>
                <div class="card-body py-3">
                    @if(isset($plaintes) && count($plaintes) > 0)
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Type de plainte</th>
                                <th>Objet</th>
                                <th>Citoyen</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plaintes as $plainte)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $plainte->type_plainte_label ?? ucfirst(str_replace('_', ' ', $plainte->type_plainte)) }}</span>
                                </td>
                                <td>{{ Str::limit($plainte->sujet ?? $plainte->objet, 50) }}</td>
                                <td>{{ Str::limit($plainte->user->name ?? __('app.unknown_user') ?? 'Utilisateur inconnu', 25) }}</td>
                                <td>
                                    <span class="badge {{ $plainte->statut === 'traite' ? 'bg-success' : ($plainte->statut === 'en_cours' ? 'bg-primary' : ($plainte->statut === 'en_attente' ? 'bg-warning' : 'bg-secondary')) }}">
                                        {{ $plainte->statut_label ?? ucfirst(str_replace('_', ' ', $plainte->statut)) }}
                                    </span>
                                </td>
                                <td>{{ $plainte->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.plaintes.show', $plainte) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune plainte trouvée</p>
                    </div>
                    @endif

                    @if(isset($plaintes) && $plaintes->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $plaintes->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection