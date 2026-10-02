@extends('layouts.app')

@section('title', 'Signaler un incident')

@section('content')
<div class="page-header">
    <div>
        <h1>Signaler un incident</h1>
        <p>Panne, rue bloquée, déchets dangereux… L’administration est prévenue immédiatement.</p>
    </div>
</div>

@if($itineraires->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-route" aria-hidden="true"></i>
            <p>Un incident se rattache à une tournée. Vous n’avez aucune tournée planifiée ou en cours.</p>
            <a href="{{ route('collecteur.itineraires.index') }}" class="btn btn-outline-primary btn-sm">Mes tournées</a>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-xl-8">
            <form method="POST" action="{{ route('collecteur.incidents.store') }}" enctype="multipart/form-data" class="card">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="itineraire_id" class="form-label">Tournée</label>
                            <select class="form-select @error('itineraire_id') is-invalid @enderror" id="itineraire_id" name="itineraire_id" required>
                                @foreach($itineraires as $it)
                                    <option value="{{ $it->id }}" @selected((int) old('itineraire_id', $selection) === $it->id)>
                                        {{ $it->nom }} — {{ $it->statut === 'en_cours' ? 'en cours' : $it->date_debut?->format('d/m') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('itineraire_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type_incident" class="form-label">Type d’incident</label>
                            <select class="form-select @error('type_incident') is-invalid @enderror" id="type_incident" name="type_incident" required>
                                <option value="">Choisir…</option>
                                @foreach(['panne_vehicule' => 'Panne du véhicule', 'probleme_acces' => 'Accès impossible (rue bloquée, inondée…)', 'dechet_non_collectable' => 'Déchets non collectables (dangereux, trop volumineux)', 'autre' => 'Autre'] as $valeur => $libelle)
                                    <option value="{{ $valeur }}" @selected(old('type_incident') === $valeur)>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @error('type_incident')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Que se passe-t-il ?</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required maxlength="1000" placeholder="Ex. : pneu crevé au carrefour de Bè-Kpota, besoin d’un dépannage">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <x-carte.choix :latitude="old('latitude')" :longitude="old('longitude')" adresse="" quartier=""
                                           label="Lieu de l’incident (facultatif)"
                                           aide="Utilisez « Ma position » si vous êtes sur place." />
                        </div>
                        <div class="col-12">
                            <label for="photo" class="form-label">Photo <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" capture="environment">
                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-danger">Envoyer à l’administration</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
