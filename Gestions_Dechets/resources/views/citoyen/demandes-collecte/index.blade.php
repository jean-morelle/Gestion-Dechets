@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mes Demandes de Collecte</h5>
                    <a href="{{ route('citoyen.demandes-collecte.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Nouvelle Demande
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($demandesCollecte) && count($demandesCollecte) > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandesCollecte as $demande)
                            <tr>
                                <td>{{ Str::limit($demande->description ?? $demande->objet, 50) }}</td>
                                <td>{{ $demande->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $demande->statut_class ?? 'bg-warning' }}">
                                        {{ $demande->statut_label ?? __('reports.pending') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('citoyen.demandes-collecte.show', $demande->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune demande de collecte</p>
                        <a href="{{ route('citoyen.demandes-collecte.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Effectuer la première demande
                        </a>
                    </div>
                    @endif

                    @if(isset($demandesCollecte) && $demandesCollecte->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $demandesCollecte->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection















