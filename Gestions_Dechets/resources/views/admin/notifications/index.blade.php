@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-header">
    <div>
        <h1>Notifications</h1>
        <p>Alertes de l’application et messages envoyés aux habitants et aux agents.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary"><i class="fas fa-paper-plane me-1" aria-hidden="true"></i>Envoyer un message</a>
        <a href="{{ route('admin.notifications.urgence') }}" class="btn btn-outline-danger"><i class="fas fa-bullhorn me-1" aria-hidden="true"></i>Alerte à tous</a>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <ul class="nav nav-pills small">
        <li class="nav-item">
            <a class="nav-link {{ $onglet === 'recues' ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                Reçues @if($nonLues > 0)<span class="badge rounded-pill text-bg-light ms-1">{{ $nonLues }}</span>@endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $onglet === 'envoyees' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['onglet' => 'envoyees']) }}">Envoyées</a>
        </li>
    </ul>
    @if($onglet === 'recues' && $nonLues > 0)
        <form method="POST" action="{{ route('admin.notifications.tout-lu') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-link">Tout marquer comme lu</button>
        </form>
    @endif
</div>

<div class="card">
    @if($notifications->isEmpty())
        <div class="empty-state">
            <i class="fas fa-bell-slash" aria-hidden="true"></i>
            <p>{{ $onglet === 'envoyees' ? 'Aucun message envoyé pour l’instant.' : 'Aucune alerte. Vous serez prévenu ici des nouveaux signalements, incidents et tournées terminées.' }}</p>
        </div>
    @else
        <ul class="activity-list">
            @foreach($notifications as $n)
                <li @class(['notif-non-lue' => $onglet === 'recues' && $n->statut === 'non_lu'])>
                    <div class="flex-grow-1 min-w-0">
                        @if($onglet === 'recues')
                            <a href="{{ route('admin.notifications.ouvrir', $n) }}" class="d-block">{{ $n->titre }}</a>
                        @else
                            <div class="fw-medium">{{ $n->titre }}</div>
                        @endif
                        <div class="small text-body-secondary">{{ $n->message }}</div>
                        <div class="activity-meta">
                            @if($onglet === 'envoyees')À {{ $n->user->name ?? 'compte supprimé' }} · {{ $n->statut === 'non_lu' ? 'pas encore lu' : 'lu' }} · @endif
                            {{ $n->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($onglet === 'envoyees')
                        <form action="{{ route('admin.notifications.destroy', $n) }}" method="POST" onsubmit="return confirm('Retirer ce message ? Le destinataire ne le verra plus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger" aria-label="Retirer"><i class="fas fa-trash" aria-hidden="true"></i></button>
                        </form>
                    @elseif($n->statut === 'non_lu')
                        <span class="badge tone-blue">Nouveau</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
