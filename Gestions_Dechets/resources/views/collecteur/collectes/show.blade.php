@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="fas fa-recycle me-2"></i>Détails de la Collecte
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('collecteur.collectes.index') }}">Mes Collectes</a>
                        </li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Détails de la Collecte</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Type de déchet</h6>
                            <p class="text-muted">{{ $collecte->type_dechet_label ?? 'Non spécifié' }}</p>

                            <h6>Quantité collectée</h6>
                            <p class="text-muted">{{ $collecte->quantite ?? 'Non spécifié' }} {{ $collecte->unite_mesure_label ?? '' }}</p>

                            <h6>Date</h6>
                            <p class="text-muted">{{ $collecte->date_collecte ? $collecte->date_collecte->format('d/m/Y') : 'Non spécifié' }}</p>

                            <h6>Heure de début</h6>
                            <p class="text-muted">{{ $collecte->heure_debut ? $collecte->heure_debut->format('H:i') : 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Heure de fin</h6>
                            <p class="text-muted">{{ $collecte->heure_fin ? $collecte->heure_fin->format('H:i') : 'Non spécifié' }}</p>

                            <h6>Durée de collecte</h6>
                            <p class="text-muted">{{ $collecte->duree_collecte ? $collecte->duree_collecte . ' minutes' : 'Non calculée' }}</p>

                            <h6>Distance parcourue</h6>
                            <p class="text-muted">{{ $collecte->distance_collecte ? $collecte->distance_collecte . ' km' : 'Non calculée' }}</p>

                            <h6>Validation GPS</h6>
                            <p class="text-muted">
                                @if($collecte->validation_gps)
                                    <span class="badge bg-success">Validée</span>
                                @else
                                    <span class="badge bg-warning">Non validée</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($collecte->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Notes</h6>
                            <p class="text-muted">{{ $collecte->notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($collecte->photo_avant || $collecte->photo_apres)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Photos</h6>
                            <div class="row">
                                @if($collecte->photo_avant)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Photo avant</h6>
                                    <img src="{{ Storage::url($collecte->photo_avant) }}" alt="Photo avant" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                @endif
                                @if($collecte->photo_apres)
                                <div class="col-md-6">
                                    <h6 class="text-muted">Photo après</h6>
                                    <img src="{{ Storage::url($collecte->photo_apres) }}" alt="Photo après" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($collecte->latitude && $collecte->longitude)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Position GPS</h6>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Latitude: {{ $collecte->latitude }}, Longitude: {{ $collecte->longitude }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($collecte->itineraire)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de l'Itinéraire</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Nom</h6>
                            <p class="text-muted">{{ $collecte->itineraire->nom ?? 'Non spécifié' }}</p>

                            <h6>Type</h6>
                            <p class="text-muted">{{ $collecte->itineraire->type_label ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Statut</h6>
                            <p class="text-muted">
                                <span class="badge {{ $collecte->itineraire->statut_class ?? 'bg-secondary' }}">
                                    {{ $collecte->itineraire->statut_label ?? 'Non défini' }}
                                </span>
                            </p>

                            <h6>Date prévue</h6>
                            <p class="text-muted">{{ $collecte->itineraire->date_debut ? $collecte->itineraire->date_debut->format('d/m/Y') : 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($collecte->pointDeCollecte)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Point de collecte</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Nom</h6>
                            <p class="text-muted">{{ $collecte->pointDeCollecte->nom ?? 'Non spécifié' }}</p>

                            <h6>Adresse</h6>
                            <p class="text-muted">{{ $collecte->pointDeCollecte->adresse ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Type de point</h6>
                            <p class="text-muted">{{ $collecte->pointDeCollecte->type ?? 'Non spécifié' }}</p>

                            <h6>Capacité</h6>
                            <p class="text-muted">{{ $collecte->pointDeCollecte->capacite ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statut et Actions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Statut actuel</h6>
                        <span class="badge {{ $collecte->statut_class ?? 'bg-secondary' }}">
                            {{ $collecte->statut_label ?? 'Non défini' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <h6>Créé le</h6>
                        <p class="text-muted">{{ $collecte->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($collecte->updated_at && $collecte->updated_at != $collecte->created_at)
                    <div class="mb-3">
                        <h6>Dernière mise à jour</h6>
                        <p class="text-muted">{{ $collecte->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($collecte->statut === 'prevue')
                        <form method="POST" action="{{ route('collecteur.collectes.start', $collecte->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-play me-2"></i>Démarrer la collecte
                            </button>
                        </form>
                        @elseif($collecte->statut === 'en_cours')
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#validateCollecteModal">
                            <i class="fas fa-check me-2"></i>Valider la collecte
                        </button>
                        
                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="fas fa-edit me-2"></i>Mettre à jour le statut
                        </button>
                        @elseif($collecte->statut === 'termine')
                        <button class="btn btn-success w-100" disabled>
                            <i class="fas fa-check-circle me-2"></i>Collecte terminée
                        </button>
                        @endif

                        <a href="{{ route('collecteur.incidents.create') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Signaler un incident
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Collecteur</h5>
                </div>
                <div class="card-body">
                    <h6>Nom</h6>
                    <p class="text-muted">{{ $collecte->collecteur->name ?? 'Non spécifié' }}</p>

                    <h6>Email</h6>
                    <p class="text-muted">{{ $collecte->collecteur->email ?? 'Non spécifié' }}</p>

                    @if($collecte->collecteur->telephone)
                    <h6>Téléphone</h6>
                    <p class="text-muted">{{ $collecte->collecteur->telephone }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('collecteur.collectes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                        
                        @if($collecte->itineraire)
                        <a href="{{ route('collecteur.itineraires.show', $collecte->itineraire->id) }}" class="btn btn-info">
                            <i class="fas fa-route me-2"></i>Voir l'itinéraire
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Mettre à jour le statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('collecteur.collectes.mettre-a-jour', $collecte->id) }}">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="statut" class="form-label">Nouveau statut</label>
                        <select class="form-select" id="statut" name="statut" required>
                            <option value="">Sélectionnez un statut</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Collecte terminée</option>
                            <option value="annule">Annulé</option>
                            <option value="en_pause">En pause</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ajoutez des notes sur la collecte..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="validateCollecteModal" tabindex="-1" aria-labelledby="validateCollecteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validateCollecteModalLabel">Valider la collecte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('collecteur.collectes.validate', $collecte->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">Votre position GPS sera enregistrée automatiquement.</div>
                    <div class="mb-3">
                        <label class="form-label">Quantité collectée</label>
                        <input type="number" step="0.01" name="quantite" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type de déchet</label>
                        <select name="type_dechet_collecte" class="form-select" required>
                            <option value="dechet_menager">Déchets ménagers</option>
                            <option value="dechet_vert">Déchets verts</option>
                            <option value="encombrant">Encombrants</option>
                            <option value="dechet_dangereux">Déchets dangereux</option>
                            <option value="dechet_recyclable">Recyclables</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo de validation</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" capture="environment" required>
                    </div>
                    <input type="hidden" name="latitude" id="val_latitude">
                    <input type="hidden" name="longitude" id="val_longitude">
                    <input type="hidden" name="accuracy" id="val_accuracy">
                    <div id="gps_status" class="small text-muted"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('validateCollecteModal');
    if (!modal) return;
    modal.addEventListener('shown.bs.modal', function () {
        const status = document.getElementById('gps_status');
        if (!navigator.geolocation) {
            status.textContent = "La géolocalisation n'est pas supportée.";
            return;
        }
        status.textContent = 'Récupération de votre position...';
        navigator.geolocation.getCurrentPosition(function (pos) {
            document.getElementById('val_latitude').value = pos.coords.latitude;
            document.getElementById('val_longitude').value = pos.coords.longitude;
            document.getElementById('val_accuracy').value = pos.coords.accuracy;
            status.textContent = 'Position obtenue (' + pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5) + ')';
        }, function (err) {
            status.textContent = 'Impossible d\'obtenir la position (' + err.message + ')';
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    });
});
</script>
@endsection
@endsection

















