@extends('layouts.app')

@section('title', 'Signalement — ' . $signalement->type_dechet_label)

@php
    $statuts = \App\Http\Controllers\AdminSignalementController::STATUTS;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $signalement->type_dechet_label }}
            <span class="badge {{ ['en_attente' => 'tone-amber', 'en_cours' => 'tone-blue', 'traite' => 'tone-green'][$signalement->statut] ?? 'tone-slate' }} align-middle fs-6">{{ $statuts[$signalement->statut] ?? $signalement->statut }}</span>
        </h1>
        <p><a href="{{ route('admin.signalements.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Signalements</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Description</dt>
                    <dd class="col-sm-8">{{ $signalement->description }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Adresse</dt>
                    <dd class="col-sm-8">{{ $signalement->adresse }}, {{ $signalement->quartier }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Signalé par</dt>
                    <dd class="col-sm-8">
                        {{ $signalement->user->name ?? 'Compte supprimé' }}
                        @if($signalement->user?->telephone)
                            · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $signalement->user->telephone) }}">{{ $signalement->user->telephone }}</a>
                        @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Reçu le</dt>
                    <dd class="col-sm-8 mb-0">{{ $signalement->created_at->translatedFormat('j F Y à H:i') }}</dd>
                </dl>
            </div>
        </div>

        @if($signalement->photo)
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $signalement->photo) }}" alt="Photo jointe au signalement" class="img-fluid rounded">
                </div>
            </div>
        @endif

        @if($signalement->latitude && $signalement->longitude)
            <x-carte.apercu :latitude="$signalement->latitude" :longitude="$signalement->longitude" :libelle="$signalement->adresse" hauteur="280px" />
        @endif
    </div>

    <div class="col-xl-5">
        <form method="POST" action="{{ route('admin.signalements.update', $signalement) }}" class="card">
            @csrf
            @method('PUT')
            <div class="card-header"><h5>Traitement</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            @foreach($statuts as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('statut', $signalement->statut) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="priorite" class="form-label">Urgence</label>
                        <select class="form-select" id="priorite" name="priorite">
                            @foreach(['faible' => 'Faible', 'moyenne' => 'Moyenne', 'elevee' => 'Élevée', 'urgente' => 'Urgente'] as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('priorite', $signalement->priorite) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="date_collecte_prevue" class="form-label">Intervention prévue le <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="date" class="form-control @error('date_collecte_prevue') is-invalid @enderror" id="date_collecte_prevue" name="date_collecte_prevue"
                               value="{{ old('date_collecte_prevue', $signalement->date_collecte_prevue?->toDateString()) }}">
                        @error('date_collecte_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="notes_admin" class="form-label">Message au citoyen <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <textarea class="form-control @error('notes_admin') is-invalid @enderror" id="notes_admin" name="notes_admin" rows="3"
                                  placeholder="Ex. : une équipe passera jeudi matin">{{ old('notes_admin', $signalement->notes_admin) }}</textarea>
                        @error('notes_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @if($signalement->date_collecte_reelle)
                    <div class="form-text mt-2">Traité le {{ $signalement->date_collecte_reelle->translatedFormat('j F Y à H:i') }}.</div>
                @endif
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Enregistrer et prévenir le citoyen</button>
            </div>
        </form>
    </div>
</div>
@endsection
