@extends('layouts.app')

@section('title', 'Campagnes de Sensibilisation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Campagnes de Sensibilisation</h4>
                    <p class="text-muted mb-0">Découvrez les campagnes de sensibilisation pour l'environnement</p>
                </div>
            </div>

            <!-- Liste des campagnes -->
            <div class="row">
                @forelse($campagnes as $campagne)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            @if($campagne->image_preview)
                                <img src="{{ asset('storage/' . $campagne->image_preview) }}" 
                                     class="card-img-top" 
                                     alt="{{ $campagne->titre }}" 
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-{{ $campagne->type === 'video' ? 'play' : ($campagne->type === 'affiche' ? 'image' : 'file') }} fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $campagne->titre }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($campagne->description, 100) }}</p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-{{ $campagne->statut_class }}">{{ $campagne->statut_label }}</span>
                                    <span class="badge bg-secondary">{{ $campagne->type_label }}</span>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Vues</small>
                                        <strong>{{ $campagne->vues }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Partages</small>
                                        <strong>{{ $campagne->partages }}</strong>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('citoyen.campagnes.show', $campagne) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>Voir la campagne
                                        </a>
                                        <form action="{{ route('citoyen.campagnes.partager', $campagne) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success w-100">
                                                <i class="fas fa-share me-1"></i>Partager
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer text-muted">
                                <small>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $campagne->created_at->format('d/m/Y') }}
                                    @if($campagne->date_fin)
                                        - Fin: {{ $campagne->date_fin->format('d/m/Y') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune campagne disponible</h5>
                            <p class="text-muted">Aucune campagne de sensibilisation n'est actuellement active.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($campagnes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $campagnes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection