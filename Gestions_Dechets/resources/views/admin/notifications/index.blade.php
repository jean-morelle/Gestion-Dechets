@extends('layouts.app')

@section('title', 'Gestion des Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Gestion des Notifications</h4>
                    <p class="text-muted mb-0">Envoyez et gérez les notifications aux utilisateurs</p>
                </div>
                <div>
                    <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus me-1"></i>Nouvelle Notification
                    </a>
                    <a href="{{ route('admin.notifications.urgence') }}" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>Notification d'Urgence
                    </a>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $statistiques['total'] }}</h4>
                                    <p class="mb-0">Total envoyées</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-paper-plane fa-2x"></i>
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
                                    <h4 class="mb-0">{{ $statistiques['non_lues'] }}</h4>
                                    <p class="mb-0">Non lues</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-envelope fa-2x"></i>
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
                                    <h4 class="mb-0">{{ $statistiques['lues'] }}</h4>
                                    <p class="mb-0">Lues</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-envelope-open fa-2x"></i>
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
                                    <h4 class="mb-0">{{ number_format(($statistiques['lues'] / max($statistiques['total'], 1)) * 100, 1) }}%</h4>
                                    <p class="mb-0">Taux de lecture</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des notifications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Notifications Envoyées</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="{{ $notification->icone_a_afficher }} me-2 text-primary"></i>
                                        <h6 class="mb-0">{{ $notification->titre }}</h6>
                                        <span class="badge {{ $notification->statut_class }} ms-2">
                                            {{ $notification->statut_label }}
                                        </span>
                                        <span class="badge bg-{{ $notification->priorite === 'urgente' ? 'danger' : ($notification->priorite === 'elevee' ? 'warning' : 'secondary') }} ms-2">
                                            {{ $notification->priorite_label }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-muted">{{ Str::limit($notification->message, 150) }}</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-user me-1"></i>
                                        <span class="me-3">Destinataire: {{ $notification->user->name }}</span>
                                        <i class="fas fa-clock me-1"></i>
                                        <span>{{ $notification->temps_ecoule }}</span>
                                        @if($notification->lien_action)
                                            <a href="{{ $notification->lien_action }}" class="btn btn-sm btn-outline-primary ms-3">
                                                <i class="fas fa-external-link-alt me-1"></i>Voir
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.notifications.show', $notification) }}" 
                                           class="btn btn-sm btn-outline-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $notification) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer cette notification ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune notification envoyée</h5>
                            <p class="text-muted">Commencez par envoyer votre première notification.</p>
                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Créer une notification
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection















