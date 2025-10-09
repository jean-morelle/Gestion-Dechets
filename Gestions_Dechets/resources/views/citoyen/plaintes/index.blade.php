@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mes Plaintes</h5>
                    <a href="{{ route('citoyen.plaintes.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>Nouvelle Plainte
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($plaintes) && count($plaintes) > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type de plainte</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plaintes as $plainte)
                            <tr>
                                <td>{{ $plainte->type_plainte_label ?? 'Non spécifié' }}</td>
                                <td>{{ Str::limit($plainte->description, 50) }}</td>
                                <td>{{ $plainte->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $plainte->statut_class ?? 'bg-warning' }}">
                                        {{ $plainte->statut_label ?? __('reports.pending') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $plainte->priorite_class ?? 'bg-info' }}">
                                        {{ $plainte->priorite_label ?? __('reports.medium') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('citoyen.plaintes.show', $plainte->id) }}">
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
                        <a href="{{ route('citoyen.plaintes.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-2"></i>Déposer une plainte
                        </a>
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















