@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-4">
                <a href="{{ route('citoyen.plaintes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('app.back_to_list') ?? __('app.back') }}
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="card">
                        <div class="card-header bg-warning text-dark py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                Plaintes #{{ $plainte->id }}
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Type de plainte</h6>
                                        <span class="badge bg-info">{{ $plainte->type_plainte_label }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">{{ __('complaints.subject') ?? 'Sujet' }}</h6>
                                        <p class="mb-0">{{ $plainte->sujet }}</p>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">{{ __('complaints.description') }}</h6>
                                        <p class="mb-0">{{ $plainte->description }}</p>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Adresse</h6>
                                        <p class="mb-0">{{ $plainte->adresse }}</p>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Quartier</h6>
                                        <p class="mb-0">{{ $plainte->quartier }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Statut</h6>
                                        <span class="badge {{ $plainte->statut_class }}">{{ $plainte->statut_label }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Priorité</h6>
                                        <span class="badge {{ $plainte->priorite_class }}">{{ $plainte->priorite_label }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Créé le</h6>
                                        <p class="mb-0">{{ $plainte->created_at->format('d/m/Y à H:i') }}</p>
                                    </div>

                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Dernière mise à jour</h6>
                                        <p class="mb-0">{{ $plainte->updated_at->format('d/m/Y à H:i') }}</p>
                                    </div>

                                    @if($plainte->contact_telephone)
                                    <div class="mb-2">
                                        <h6 class="text-muted small mb-1">Téléphone</h6>
                                        <p class="mb-0">{{ $plainte->contact_telephone }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Coordonnées GPS -->
                            @if($plainte->latitude && $plainte->longitude)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <h6 class="text-muted small mb-1">Position GPS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Latitude:</small>
                                            <p class="mb-0 small">{{ $plainte->latitude }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Longitude:</small>
                                            <p class="mb-0 small">{{ $plainte->longitude }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Photo -->
                            @if($plainte->photo)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <h6 class="text-muted small mb-1">Photo</h6>
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $plainte->photo) }}" 
                                             alt="Plaintes" 
                                             class="img-fluid rounded" 
                                             style="max-height: 200px;">
                                    </div>
                                </div>
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














