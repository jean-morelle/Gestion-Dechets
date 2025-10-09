@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Notifications</h4>
                    <p class="text-muted mb-0">Restez informé de vos activités</p>
                </div>
                <div>
                    @if($notifications->where('read_at', null)->count() > 0)
                        <button class="btn btn-outline-primary btn-sm" onclick="marquerToutesLues()">
                            <i class="fas fa-check-double me-1"></i>Marquer toutes comme lues
                        </button>
                    @endif
                </div>
            </div>

            <!-- Liste des notifications -->
            <div class="card">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-3 border-bottom {{ $notification->statut === 'non_lu' ? 'bg-light' : '' }}" 
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if($notification->statut === 'lu')
                                            <i class="fas fa-envelope-open text-muted me-2"></i>
                                        @else
                                            <i class="fas fa-envelope text-primary me-2"></i>
                                        @endif
                                        <h6 class="mb-0 {{ $notification->statut === 'lu' ? 'text-muted' : 'fw-bold' }}">
                                            {{ $notification->titre }}
                                        </h6>
                                        @if($notification->statut === 'non_lu')
                                            <span class="badge bg-primary ms-2">Nouveau</span>
                                        @endif
                                    </div>
                                    <p class="mb-2 {{ $notification->statut === 'lu' ? 'text-muted' : '' }}">
                                        {{ $notification->message }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="ms-3">
                                    @if($notification->statut === 'non_lu')
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="marquerLue({{ $notification->id }})"
                                                title="Marquer comme lue">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune notification</h5>
                            <p class="text-muted">Vous n'avez pas encore de notifications.</p>
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

<script>
function marquerLue(notificationId) {
    fetch(`/collecteur/notifications/${notificationId}/marquer-lue`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'interface
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-light');
                const icon = notificationItem.querySelector('.fa-envelope');
                if (icon) {
                    icon.classList.replace('fa-envelope', 'fa-envelope-open');
                    icon.classList.add('text-muted');
                }
                const title = notificationItem.querySelector('h6');
                if (title) {
                    title.classList.remove('fw-bold');
                    title.classList.add('text-muted');
                }
                const message = notificationItem.querySelector('p');
                if (message) {
                    message.classList.add('text-muted');
                }
                const badge = notificationItem.querySelector('.badge');
                if (badge) {
                    badge.remove();
                }
                const button = notificationItem.querySelector('button');
                if (button) {
                    button.remove();
                }
            }
            
            // Mettre à jour le compteur de notifications non lues
            updateNotificationCount();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour de la notification');
    });
}

function marquerToutesLues() {
    if (confirm('Marquer toutes les notifications comme lues ?')) {
        fetch('/collecteur/notifications/marquer-toutes-lues', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour des notifications');
        });
    }
}

function updateNotificationCount() {
    // Cette fonction peut être utilisée pour mettre à jour le compteur de notifications
    // dans la navbar si nécessaire
}
</script>
@endsection
