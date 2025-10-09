@extends('layouts.app')

@section('title', 'Statistiques de la Campagne')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Statistiques : {{ $campagne->titre }}</h4>
                    <p class="text-muted mb-0">Analyse des performances de la campagne</p>
                </div>
                <div>
                    <a href="{{ route('admin.campagnes.show', $campagne) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>

            <!-- Métriques principales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $statistiques['vues'] }}</h4>
                                    <p class="mb-0">Vues totales</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $statistiques['partages'] }}</h4>
                                    <p class="mb-0">Partages</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-share fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $statistiques['collecteurs_actifs'] }}</h4>
                                    <p class="mb-0">Collecteurs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $statistiques['citoyens_cibles'] }}</h4>
                                    <p class="mb-0">Citoyens cibles</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Informations de la campagne -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informations de la campagne</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Type :</strong></td>
                                    <td><span class="badge bg-secondary">{{ $campagne->type_label }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td><span class="badge bg-{{ $campagne->statut_class }}">{{ $campagne->statut_label }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Créée le :</strong></td>
                                    <td>{{ $campagne->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                @if($campagne->date_debut)
                                    <tr>
                                        <td><strong>Début :</strong></td>
                                        <td>{{ $campagne->date_debut->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                                @if($campagne->date_fin)
                                    <tr>
                                        <td><strong>Fin :</strong></td>
                                        <td>{{ $campagne->date_fin->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Dernière modification :</strong></td>
                                    <td>{{ $campagne->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quartiers cibles -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quartiers cibles</h5>
                        </div>
                        <div class="card-body">
                            @if($campagne->quartiers_cibles && count($campagne->quartiers_cibles) > 0)
                                @foreach($campagne->quartiers_cibles as $quartier)
                                    <span class="badge bg-primary me-1 mb-1">{{ $quartier }}</span>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">Tous les quartiers</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et analyses -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Analyse des performances</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Taux d'engagement</h6>
                                    @php
                                        $tauxEngagement = $statistiques['vues'] > 0 ? ($statistiques['partages'] / $statistiques['vues']) * 100 : 0;
                                    @endphp
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ min($tauxEngagement, 100) }}%">
                                            {{ number_format($tauxEngagement, 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Partages par vue</small>
                                </div>
                                <div class="col-md-6">
                                    <h6>Portée potentielle</h6>
                                    <div class="text-center">
                                        <h3 class="text-primary">{{ $statistiques['citoyens_cibles'] }}</h3>
                                        <small class="text-muted">Citoyens pouvant être touchés</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions recommandées -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recommandations</h5>
                        </div>
                        <div class="card-body">
                            @if($statistiques['vues'] == 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Action requise :</strong> Cette campagne n'a pas encore été vue. Assurez-vous que les collecteurs la partagent avec les citoyens.
                                </div>
                            @elseif($statistiques['partages'] == 0)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Amélioration possible :</strong> La campagne est vue mais pas partagée. Encouragez les collecteurs à la partager activement.
                                </div>
                            @elseif($tauxEngagement < 5)
                                <div class="alert alert-warning">
                                    <i class="fas fa-chart-line me-2"></i>
                                    <strong>Engagement faible :</strong> Le taux de partage est faible. Considérez améliorer le contenu ou la communication.
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Excellent !</strong> La campagne performe bien avec un bon taux d'engagement.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection















