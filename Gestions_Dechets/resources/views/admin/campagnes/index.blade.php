@extends('layouts.app')

@section('title', 'Campagnes')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Campagnes</h3>
            <p>Gestion des campagnes de sensibilisation</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['total'] }}</h4>
                    <p>Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['actives'] }}</h4>
                    <p>Actives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['brouillons'] }}</h4>
                    <p>Brouillons</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['terminees'] }}</h4>
                    <p>Terminées</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Liste des campagnes</h5>
                    <a href="{{ route('admin.campagnes.create') }}" class="btn btn-primary">Nouvelle campagne</a>
                </div>
                <div class="card-body">
                    @forelse($campagnes as $campagne)
                        <div class="border-bottom py-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>{{ $campagne->titre }}</h6>
                                    <p class="text-muted mb-2">{{ Str::limit($campagne->description, 100) }}</p>
                                    <span class="badge bg-{{ $campagne->statut_class }}">{{ $campagne->statut_label }}</span>
                                    <span class="badge bg-secondary">{{ $campagne->type_label }}</span>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('admin.campagnes.show', $campagne) }}" class="btn btn-sm btn-outline-info me-1">Voir</a>
                                    <a href="{{ route('admin.campagnes.edit', $campagne) }}" class="btn btn-sm btn-outline-primary me-1">Modifier</a>
                                    @if($campagne->statut === 'brouillon')
                                        <form action="{{ route('admin.campagnes.publier', $campagne) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Publier</button>
                                        </form>
                                    @elseif($campagne->statut === 'active')
                                        <form action="{{ route('admin.campagnes.archiver', $campagne) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">Archiver</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.campagnes.destroy', $campagne) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette campagne ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p>Aucune campagne trouvée</p>
                            <a href="{{ route('admin.campagnes.create') }}" class="btn btn-primary">Créer une campagne</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($campagnes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $campagnes->links() }}
        </div>
    @endif
</div>
@endsection