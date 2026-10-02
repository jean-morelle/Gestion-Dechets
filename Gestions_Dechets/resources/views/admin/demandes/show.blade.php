@extends('layouts.app')

@section('title', $demande->objet)

@php
    $modifiable = in_array($demande->statut, ['en_attente', 'accepte'], true);
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $demande->objet }} <span class="badge {{ $demande->statut_tone }} align-middle fs-6">{{ $demande->statut_label }}</span></h1>
        <p><a href="{{ route('admin.demandes.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Demandes de collecte</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Type</dt>
                    <dd class="col-sm-8">{{ $demande->type_collecte_label }} · <span class="badge {{ $demande->urgence_tone }}">Urgence {{ mb_strtolower($demande->urgence_label) }}</span></dd>

                    <dt class="col-sm-4 fw-normal text-body-secondary">Description</dt>
                    <dd class="col-sm-8">{{ $demande->description }}</dd>

                    @if($demande->instructions_speciales)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Consignes</dt>
                        <dd class="col-sm-8">{{ $demande->instructions_speciales }}</dd>
                    @endif

                    <dt class="col-sm-4 fw-normal text-body-secondary">Adresse</dt>
                    <dd class="col-sm-8">{{ $demande->adresse }}, {{ $demande->quartier }}</dd>

                    @if($demande->date_souhaitee)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Souhaitée</dt>
                        <dd class="col-sm-8">{{ $demande->date_souhaitee->translatedFormat('l j F') }}@if($demande->heure_souhaitee) vers {{ $demande->heure_souhaitee->format('H:i') }}@endif</dd>
                    @endif

                    <dt class="col-sm-4 fw-normal text-body-secondary">Demandeur</dt>
                    <dd class="col-sm-8">
                        {{ $demande->user->name ?? 'Compte supprimé' }}
                        @if($demande->contact_telephone)
                            · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $demande->contact_telephone) }}">{{ $demande->contact_telephone }}</a>
                        @endif
                    </dd>

                    <dt class="col-sm-4 fw-normal text-body-secondary">Reçue le</dt>
                    <dd class="col-sm-8 mb-0">{{ $demande->created_at->translatedFormat('j F Y à H:i') }}</dd>
                </dl>
            </div>
        </div>

        @if($demande->photo)
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $demande->photo) }}" alt="Photo jointe par le citoyen" class="img-fluid rounded">
                </div>
            </div>
        @endif

        @if($demande->latitude && $demande->longitude)
            <x-carte.apercu :latitude="$demande->latitude" :longitude="$demande->longitude" :libelle="$demande->adresse" hauteur="260px" />
        @endif
    </div>

    <div class="col-xl-5">
        @if($demande->statut === 'refuse')
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Demande refusée</h2>
                    <p class="mb-1">{{ $demande->raison_refus }}</p>
                    <p class="small text-body-secondary mb-0">Par {{ $demande->admin->name ?? '—' }}, le {{ $demande->date_traitement?->translatedFormat('j F Y') }}</p>
                </div>
            </div>
        @elseif($demande->statut === 'termine')
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Collecte effectuée</h2>
                    <p class="small text-body-secondary mb-0">
                        Planifiée par {{ $demande->admin->name ?? '—' }} pour le {{ $demande->date_collecte_prevue?->translatedFormat('j F Y') }}
                        @if($demande->collecteur) · {{ $demande->collecteur->name }} @endif
                    </p>
                </div>
            </div>
        @endif

        @if($modifiable)
            <form method="POST" action="{{ route('admin.demandes.accepter', $demande) }}" class="card mb-4">
                @csrf
                <div class="card-header"><h5>{{ $demande->statut === 'accepte' ? 'Passage planifié' : 'Planifier le passage' }}</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="date_collecte_prevue" class="form-label">Date de passage</label>
                        <input type="date" class="form-control @error('date_collecte_prevue') is-invalid @enderror" id="date_collecte_prevue" name="date_collecte_prevue"
                               min="{{ today()->toDateString() }}"
                               value="{{ old('date_collecte_prevue', $demande->date_collecte_prevue?->toDateString() ?? $demande->date_souhaitee?->toDateString()) }}" required>
                        @error('date_collecte_prevue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="collecteur_id" class="form-label">Collecteur <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <select class="form-select" id="collecteur_id" name="collecteur_id">
                            <option value="">À définir</option>
                            @foreach($collecteurs as $c)
                                <option value="{{ $c->id }}" @selected((int) old('collecteur_id', $demande->collecteur_id) === $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer d-flex flex-wrap justify-content-between gap-2">
                    <button type="submit" class="btn btn-primary">{{ $demande->statut === 'accepte' ? 'Modifier la date' : 'Accepter et prévenir le citoyen' }}</button>
                </div>
            </form>

            @if($demande->statut === 'accepte')
                <form method="POST" action="{{ route('admin.demandes.terminer', $demande) }}" class="card mb-4">
                    @csrf
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span>La collecte a été faite ?</span>
                        <button type="submit" class="btn btn-success">Marquer comme effectuée</button>
                    </div>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.demandes.refuser', $demande) }}" class="card">
                @csrf
                <div class="card-body">
                    <label for="raison_refus" class="form-label">Refuser la demande</label>
                    <textarea class="form-control @error('raison_refus') is-invalid @enderror" id="raison_refus" name="raison_refus" rows="2" required
                              placeholder="Expliquez au citoyen pourquoi (il recevra ce message)">{{ old('raison_refus') }}</textarea>
                    @error('raison_refus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <button type="submit" class="btn btn-outline-danger btn-sm mt-2">Refuser</button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
