@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Signalements</h6>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Statut: Tous</option>
                        <option>En attente</option>
                        <option>En cours</option>
                        <option>Traités</option>
                    </select>
                </div>
                <div class="card-body py-3">
                    @if(isset($signalements) && count($signalements) > 0)
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Type de déchet</th>
                                <th>Description</th>
                                <th>Quartier</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($signalements as $signalement)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $signalement->type_dechet_label ?? $signalement->type_dechet }}</span>
                                </td>
                                <td>{{ Str::limit($signalement->description, 50) }}</td>
                                <td>{{ $signalement->quartier ?? 'Non spécifié' }}</td>
                                <td>
                                    <span class="badge {{ $signalement->statut === 'traite' ? 'bg-success' : ($signalement->statut === 'en_cours' ? 'bg-primary' : ($signalement->statut === 'en_attente' ? 'bg-warning' : 'bg-secondary')) }}">
                                        {{ $signalement->statut_label ?? ucfirst(str_replace('_', ' ', $signalement->statut)) }}
                                    </span>
                                </td>
                                <td>{{ $signalement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.signalements.show', $signalement) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun signalement trouvé</p>
                    </div>
                    @endif

                    @if(isset($signalements) && $signalements->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $signalements->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
















