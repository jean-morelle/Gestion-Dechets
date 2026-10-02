@extends('layouts.app')

@section('title', 'Calendrier des collectes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-calendar-alt me-2"></i>Calendrier de Collecte
                </h4>
                <p class="text-muted">Consultez le calendrier des collectes dans votre quartier.</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('citoyen.calendrier.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="quartier" class="form-label">Quartier</label>
                            <select class="form-select" id="quartier" name="quartier">
                                <option value="">Tous les quartiers</option>
                                @foreach($quartiers ?? [] as $q)
                                <option value="{{ $q }}" {{ request('quartier') === $q ? 'selected' : '' }}>{{ $q }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type de collecte</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous les types</option>
                                <option value="menagere" {{ request('type') === 'menagere' ? 'selected' : '' }}>Collecte ménagère</option>
                                <option value="encombrant" {{ request('type') === 'encombrant' ? 'selected' : '' }}>Encombrants</option>
                                <option value="vert" {{ request('type') === 'vert' ? 'selected' : '' }}>Déchets verts</option>
                                <option value="recyclage" {{ request('type') === 'recyclage' ? 'selected' : '' }}>Recyclage</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="mois" class="form-label">Mois</label>
                            <input type="month" class="form-control" id="mois" name="mois" value="{{ request('mois', date('Y-m')) }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrer
                            </button>
                            <a href="{{ route('citoyen.calendrier.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Effacer
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Calendrier des Collectes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type de collecte</th>
                                    <th>Quartier</th>
                                    <th>Heure</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collectes ?? [] as $collecte)
                                <tr>
                                    <td>{{ $collecte->date_prevue ? $collecte->date_prevue->format('d/m/Y') : 'Non définie' }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $collecte->type_collecte_label ?? 'Non spécifié' }}
                                        </span>
                                    </td>
                                    <td>{{ $collecte->quartier ?? 'Non spécifié' }}</td>
                                    <td>
                                        @if($collecte->heure_debut && $collecte->heure_fin)
                                            {{ $collecte->heure_debut->format('H:i') }} - {{ $collecte->heure_fin->format('H:i') }}
                                        @elseif($collecte->heure_debut)
                                            {{ $collecte->heure_debut->format('H:i') }}
                                        @else
                                            Non définie
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $collecte->statut === 'termine' ? 'bg-success' : ($collecte->statut === 'en_cours' ? 'bg-primary' : 'bg-info') }}">
                                            {{ $collecte->statut_label ?? 'Programmé' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune collecte programmée pour cette période</p>
                                        <p class="text-muted small">Les collectes seront affichées ici une fois programmées par l'administration.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Prochaines Collectes</h5>
                </div>
                <div class="card-body">
                    @forelse($prochaines_collectes ?? [] as $collecte)
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $collecte->type_collecte_label ?? 'Collecte' }}</h6>
                            <p class="text-muted mb-1">{{ $collecte->quartier ?? 'Quartier' }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $collecte->date_prevue ? $collecte->date_prevue->format('d/m/Y') : 'Date non définie' }}
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune collecte à venir</p>
                        <p class="text-muted small">Les prochaines collectes seront affichées ici.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Légende -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Légende</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">Ménagère</span>
                        <small>Collecte des déchets ménagers</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success me-2">Encombrants</span>
                        <small>Collecte des gros objets</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info me-2">Déchets verts</span>
                        <small>Collecte des déchets végétaux</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning me-2">Recyclage</span>
                        <small>Collecte des matières recyclables</small>
                    </div>
                </div>
            </div>

            <!-- Informations -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Important</h6>
                        <ul class="mb-0 small">
                            <li>Sortez vos déchets la veille au soir</li>
                            <li>Respectez les horaires de collecte</li>
                            <li>Tri sélectif obligatoire</li>
                            <li>En cas de retard, contactez le service</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

















