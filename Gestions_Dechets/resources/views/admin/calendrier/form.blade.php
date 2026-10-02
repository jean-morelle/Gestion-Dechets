@extends('layouts.app')

@php
    $edition = $calendrier->exists;
    $types = \App\Http\Controllers\AdminCalendrierController::TYPES;
    $frequences = \App\Http\Controllers\AdminCalendrierController::FREQUENCES;
    $jours = \App\Http\Controllers\AdminCalendrierController::JOURS;
@endphp

@section('title', $edition ? 'Modifier un passage' : 'Ajouter un passage')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $edition ? $calendrier->nom : 'Ajouter un passage' }}</h1>
        <p><a href="{{ route('admin.calendrier.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Calendrier des collectes</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <form method="POST" action="{{ $edition ? route('admin.calendrier.update', $calendrier) : route('admin.calendrier.store') }}" class="card" id="form-calendrier">
            @csrf
            @if($edition) @method('PUT') @endif
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="quartier" class="form-label">Quartier</label>
                        <input type="text" class="form-control @error('quartier') is-invalid @enderror" id="quartier" name="quartier" value="{{ old('quartier', $calendrier->quartier) }}" list="liste-quartiers" autocomplete="off" required>
                        <datalist id="liste-quartiers">@foreach(config('quartiers') as $q)<option value="{{ $q }}">@endforeach</datalist>
                        <div class="form-text">Les habitants inscrits dans ce quartier reçoivent le rappel.</div>
                        @error('quartier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type_collecte" class="form-label">Collecte</label>
                        <select class="form-select" id="type_collecte" name="type_collecte">
                            @foreach($types as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('type_collecte', $calendrier->type_collecte) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="frequence" class="form-label">Fréquence</label>
                        <select class="form-select" id="frequence" name="frequence">
                            @foreach($frequences as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('frequence', $calendrier->frequence) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" data-pour="hebdomadaire">
                        <label for="jour_semaine" class="form-label">Jour</label>
                        <select class="form-select @error('jour_semaine') is-invalid @enderror" id="jour_semaine" name="jour_semaine">
                            <option value="">Choisir…</option>
                            @foreach($jours as $jour)
                                <option value="{{ $jour }}" @selected(old('jour_semaine', $calendrier->jour_semaine) === $jour)>{{ ucfirst($jour) }}</option>
                            @endforeach
                        </select>
                        @error('jour_semaine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <label for="heure_debut" class="form-label">De</label>
                        <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $calendrier->heure_debut?->format('H:i')) }}" required>
                        @error('heure_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label for="heure_fin" class="form-label">À</label>
                        <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" value="{{ old('heure_fin', $calendrier->heure_fin?->format('H:i')) }}" required>
                        @error('heure_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label for="date_debut" class="form-label" data-libelle-date>À partir du</label>
                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $calendrier->date_debut?->toDateString()) }}">
                        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6 col-md-3" data-sauf="ponctuelle">
                        <label for="date_fin" class="form-label">Jusqu’au <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $calendrier->date_fin?->toDateString()) }}">
                        @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Consigne aux habitants <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $calendrier->description) }}" placeholder="Ex. : sacs fermés au bord de la voie principale">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="hidden" name="statut" value="suspendu">
                            <input class="form-check-input" type="checkbox" role="switch" id="statut" name="statut" value="actif" @checked(old('statut', $calendrier->statut) === 'actif')>
                            <label class="form-check-label" for="statut">Passage actif <span class="d-block small text-body-secondary">Décochez pour le suspendre temporairement (travaux, fête…) sans le supprimer.</span></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('admin.calendrier.index') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">{{ $edition ? 'Enregistrer' : 'Ajouter au calendrier' }}</button>
            </div>
        </form>
    </div>

    @if($edition)
        <div class="col-xl-4">
            <form method="POST" action="{{ route('admin.calendrier.destroy', $calendrier) }}" class="card" onsubmit="return confirm('Retirer ce passage du calendrier ?')">
                @csrf
                @method('DELETE')
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Retirer du calendrier</h2>
                    <p class="small text-body-secondary">Le passage disparaît du calendrier des habitants et les rappels s’arrêtent.</p>
                    <button type="submit" class="btn btn-sm btn-outline-danger">Retirer</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Champs affichés selon la fréquence choisie
    (function () {
        var frequence = document.getElementById('frequence');
        var libelleDate = document.querySelector('[data-libelle-date]');
        function maj() {
            document.querySelectorAll('[data-pour]').forEach(function (el) { el.hidden = el.dataset.pour !== frequence.value; });
            document.querySelectorAll('[data-sauf]').forEach(function (el) { el.hidden = el.dataset.sauf === frequence.value; });
            libelleDate.textContent = frequence.value === 'ponctuelle' ? 'Date' : (frequence.value === 'mensuelle' ? 'À partir du (fixe le jour du mois)' : 'À partir du');
        }
        frequence.addEventListener('change', maj);
        maj();
    })();
</script>
@endpush
