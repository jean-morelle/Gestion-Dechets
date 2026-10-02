@extends('layouts.app')

@section('title', 'Incident — ' . $incident->type_label)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $incident->type_label }} <span class="badge {{ $incident->statut === 'resolu' ? 'tone-green' : ($incident->statut === 'en_cours' ? 'tone-blue' : ($incident->statut === 'annule' ? 'tone-slate' : 'tone-amber')) }} align-middle fs-6">{{ $incident->statut_label }}</span></h1>
        <p><a href="{{ route('admin.incidents.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Incidents</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Collecteur</dt>
                    <dd class="col-sm-8">
                        {{ $incident->collecteur->name ?? 'Supprimé' }}
                        @if($incident->collecteur?->telephone)
                            · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $incident->collecteur->telephone) }}">{{ $incident->collecteur->telephone }}</a>
                        @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Tournée</dt>
                    <dd class="col-sm-8">
                        @if($incident->itineraire)
                            <a href="{{ route('admin.itineraires.show', $incident->itineraire) }}">{{ $incident->itineraire->nom }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Signalé</dt>
                    <dd class="col-sm-8">{{ $incident->created_at->translatedFormat('l j F à H:i') }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Description</dt>
                    <dd class="col-sm-8 mb-0">{{ $incident->description }}</dd>
                </dl>
            </div>
        </div>

        @if($incident->photo)
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $incident->photo) }}" alt="Photo de l’incident" class="img-fluid rounded">
                </div>
            </div>
        @endif

        @if($incident->latitude && $incident->longitude)
            <x-carte.apercu :latitude="$incident->latitude" :longitude="$incident->longitude" hauteur="260px" />
        @endif
    </div>

    <div class="col-xl-5">
        <form method="POST" action="{{ route('admin.incidents.update', $incident) }}" class="card">
            @csrf
            @method('PUT')
            <div class="card-header"><h5>Traitement</h5></div>
            <div class="card-body">
                <fieldset class="mb-3">
                    <legend class="form-label fs-6">Statut</legend>
                    @foreach(['signale' => 'Signalé', 'en_cours' => 'Pris en charge', 'resolu' => 'Résolu', 'annule' => 'Sans suite'] as $valeur => $libelle)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="statut" id="statut-{{ $valeur }}" value="{{ $valeur }}" @checked(old('statut', $incident->statut) === $valeur)>
                            <label class="form-check-label" for="statut-{{ $valeur }}">{{ $libelle }}</label>
                        </div>
                    @endforeach
                </fieldset>
                <label for="admin_notes" class="form-label">Réponse au collecteur</label>
                <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="admin_notes" name="admin_notes" rows="3"
                          placeholder="Ex. : dépanneuse envoyée, arrivée vers 10 h">{{ old('admin_notes', $incident->admin_notes) }}</textarea>
                @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($incident->date_resolution)
                    <div class="form-text">Clos le {{ $incident->date_resolution->translatedFormat('j F à H:i') }}.</div>
                @endif
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Enregistrer et prévenir le collecteur</button>
            </div>
        </form>
    </div>
</div>
@endsection
