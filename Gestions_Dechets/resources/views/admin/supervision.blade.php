@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Supervision</h3>
            <p>État du système</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['utilisateurs']['total'] }}</h4>
                    <p>Utilisateurs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['signalements']['total'] }}</h4>
                    <p>Signalements</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['plaintes']['total'] }}</h4>
                    <p>Plaintes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['itineraires']['total'] }}</h4>
                    <p>Itinéraires</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Utilisateurs</h5>
                </div>
                <div class="card-body">
                    <p>Citoyens: {{ $statistiques['utilisateurs']['citoyens'] }}</p>
                    <p>Collecteurs: {{ $statistiques['utilisateurs']['collecteurs'] }}</p>
                    <p>Admins: {{ $statistiques['utilisateurs']['admins'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Signalements</h5>
                </div>
                <div class="card-body">
                    <p>En attente: {{ $statistiques['signalements']['en_attente'] }}</p>
                    <p>Traités: {{ $statistiques['signalements']['traite'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Plaintes</h5>
                </div>
                <div class="card-body">
                    <p>En attente: {{ $statistiques['plaintes']['en_attente'] }}</p>
                    <p>Traitées: {{ $statistiques['plaintes']['traite'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Itinéraires</h5>
                </div>
                <div class="card-body">
                    <p>En cours: {{ $statistiques['itineraires']['en_cours'] }}</p>
                    <p>Terminés: {{ $statistiques['itineraires']['termine'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-primary me-2">Utilisateurs</a>
                    <a href="{{ route('admin.signalements.index') }}" class="btn btn-warning me-2">Signalements</a>
                    <a href="{{ route('admin.plaintes.index') }}" class="btn btn-info me-2">Plaintes</a>
                    <a href="{{ route('admin.itineraires.index') }}" class="btn btn-success">Itinéraires</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection