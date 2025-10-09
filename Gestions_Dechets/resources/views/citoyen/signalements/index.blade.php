@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Mes Signalements
                </h4>
                <p class="text-muted">Consultez l'historique de vos signalements et leur statut.</p>
            </div>
        </div>
    </div>

    <!-- Liste des signalements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Signalements</h5>
                    <a href="{{ route('citoyen.signalements.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Nouveau Signalement
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($signalements) && count($signalements) > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type de déchet</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($signalements as $signalement)
                            <tr>
                                <td>{{ $signalement->type_dechet_label ?? 'Non spécifié' }}</td>
                                <td>{{ Str::limit($signalement->description, 50) }}</td>
                                <td>{{ $signalement->statut_label ?? 'En attente' }}</td>
                                <td>{{ $signalement->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('citoyen.signalements.show', $signalement->id) }}">
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
                        <a href="{{ route('citoyen.signalements.create') }}" class="btn btn-danger">
                            <i class="fas fa-plus me-2"></i>Créer un Signalement
                        </a>
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
















