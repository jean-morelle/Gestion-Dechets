@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Mes Collectes</h6>
                </div>
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('collecteur.collectes.index') }}" class="row mb-3 g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small">Statut</label>
                            <select class="form-select form-select-sm" name="statut" value="{{ request('statut') }}">
                                <option value="">Tous les statuts</option>
                                <option value="prevue" {{ request('statut')==='prevue' ? 'selected' : '' }}>Prévue</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Type de collecte</label>
                            <select class="form-select form-select-sm" name="type">
                                <option value="">Tous les types</option>
                                <option value="dechet_menager" {{ request('type')==='dechet_menager' ? 'selected' : '' }}>Déchets ménagers</option>
                                <option value="encombrant" {{ request('type')==='encombrant' ? 'selected' : '' }}>Encombrants</option>
                                <option value="dechet_vert" {{ request('type')==='dechet_vert' ? 'selected' : '' }}>Déchets verts</option>
                                <option value="dechet_dangereux" {{ request('type')==='dechet_dangereux' ? 'selected' : '' }}>Déchets dangereux</option>
                                <option value="dechet_recyclable" {{ request('type')==='dechet_recyclable' ? 'selected' : '' }}>Recyclables</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Depuis le</label>
                            <input type="date" class="form-control form-control-sm" name="date_debut" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Jusqu'au</label>
                            <input type="date" class="form-control form-control-sm" name="date_fin" value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block small">&nbsp;</label>
                            <button class="btn btn-primary btn-sm w-100" type="submit">Filtrer</button>
                        </div>
                    </form>

                    @if(isset($collectes) && count($collectes) > 0)
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Type de déchet</th>
                                <th>Adresse</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Urgence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collectes as $collecte)
                            <tr>
                                <td>{{ $collecte->type_dechet_label }}</td>
                                <td>{{ Str::limit($collecte->pointDeCollecte->adresse ?? 'Non spécifié', 30) }}</td>
                                <td>{{ $collecte->date_collecte->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $collecte->statut === 'termine' ? 'bg-success' : ($collecte->statut === 'en_cours' ? 'bg-primary' : 'bg-warning') }}">
                                        {{ $collecte->statut_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $collecte->quantite }} {{ $collecte->unite_mesure }}</span>
                                </td>
                                <td>
                                    @if($collecte->statut === 'en_cours')
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#validateCollecteModal" data-id="{{ $collecte->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    <a href="{{ route('collecteur.collectes.show', $collecte->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-recycle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune collecte</p>
                    </div>
                    @endif

                    @if(isset($collectes) && $collectes->hasPages())
                    <div class="text-center mt-3">
                        <a href="{{ $collectes->nextPageUrl() }}" class="btn btn-outline-primary">Suivant</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validation -->
<div class="modal fade" id="validateCollecteModal" tabindex="-1" aria-labelledby="validateCollecteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="validateCollecteModalLabel">Valider la collecte</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="validateForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info small">Votre position GPS sera enregistrée automatiquement.</div>
                    <div class="mb-2">
                        <label class="form-label small">Quantité collectée</label>
                        <input type="number" step="0.01" name="quantite" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Type de déchet</label>
                        <select name="type_dechet_collecte" class="form-select form-select-sm" required>
                            <option value="dechet_menager">Déchets ménagers</option>
                            <option value="dechet_vert">Déchets verts</option>
                            <option value="encombrant">Encombrants</option>
                            <option value="dechet_dangereux">Déchets dangereux</option>
                            <option value="dechet_recyclable">Recyclables</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Photo de validation</label>
                        <input type="file" name="photo" class="form-control form-control-sm" accept="image/*" capture="environment" required>
                    </div>
                    <input type="hidden" name="latitude" id="val_latitude">
                    <input type="hidden" name="longitude" id="val_longitude">
                    <input type="hidden" name="accuracy" id="val_accuracy">
                    <div id="gps_status" class="small text-muted"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('validateCollecteModal');
    const form = document.getElementById('validateForm');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const collecteId = button.getAttribute('data-id');
        form.action = `{{ url('collecteur/collectes') }}/${collecteId}/validate`;

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