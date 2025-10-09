@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-eye me-2"></i>Détails de la Demande
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">{{ $demandeCollecte->objet }}</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Type de collecte :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge bg-primary">{{ $demandeCollecte->type_collecte_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Urgence :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge {{ $demandeCollecte->urgence_class }}">{{ $demandeCollecte->urgence_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Statut :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge {{ $demandeCollecte->statut_class }}">{{ $demandeCollecte->statut_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Quartier :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->quartier }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Adresse :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->adresse }}
                                </div>
                            </div>

                            @if($demandeCollecte->date_souhaitee)
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Date souhaitée :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->date_souhaitee->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($demandeCollecte->heure_souhaitee)
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Heure souhaitée :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->heure_souhaitee }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>{{ __('collections.description') ?? __('reports.description') }} :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->description }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Téléphone :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->contact_telephone }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informations</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Créé le {{ $demandeCollecte->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            Par {{ $demandeCollecte->user->name }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('citoyen.demandes-collecte.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-eye me-2"></i>Détails de la Demande
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">{{ $demandeCollecte->objet }}</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Type :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge bg-primary">{{ $demandeCollecte->type_collecte_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Urgence :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge {{ $demandeCollecte->urgence_class }}">{{ $demandeCollecte->urgence_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Statut :</strong>
                                </div>
                                <div class="col-sm-8">
                                    <span class="badge {{ $demandeCollecte->statut_class }}">{{ $demandeCollecte->statut_label }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Quartier :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->quartier }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Adresse :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->adresse }}
                                </div>
                            </div>

                            @if($demandeCollecte->date_souhaitee)
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Date souhaitée :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->date_souhaitee->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($demandeCollecte->heure_souhaitee)
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Heure souhaitée :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->heure_souhaitee }}
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Description :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->description }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Contact :</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $demandeCollecte->contact_telephone }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Informations</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Créée le {{ $demandeCollecte->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            Par {{ $demandeCollecte->user->name }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('citoyen.demandes-collecte.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
