@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Supervision</h3>
            <p>Contrôle des opérations</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Problèmes en attente</h5>
                    <p>Signalements: {{ $statistiques['signalements']['en_attente'] }}</p>
                    <p>Plaintes: {{ $statistiques['plaintes']['en_attente'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Activité actuelle</h5>
                    <p>Itinéraires: {{ $statistiques['itineraires']['en_cours'] }}</p>
                    <p>Total utilisateurs: {{ $statistiques['utilisateurs']['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Résumé</h5>
                    <p>Signalements traités: {{ $statistiques['signalements']['traite'] }}</p>
                    <p>Plaintes traitées: {{ $statistiques['plaintes']['traite'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Actions rapides</h5>
                    <a href="{{ route('admin.signalements.index') }}" class="btn btn-warning me-2">Voir les signalements</a>
                    <a href="{{ route('admin.plaintes.index') }}" class="btn btn-danger me-2">Voir les plaintes</a>
                    <a href="{{ route('admin.itineraires.index') }}" class="btn btn-info me-2">Voir les itinéraires</a>
                    <a href="{{ route('messages.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-envelope me-1"></i>Messagerie
                    </a>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary">
                        <i class="fas fa-bell me-1"></i>Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection