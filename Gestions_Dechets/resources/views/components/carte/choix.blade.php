{{--
    Sélection d'un emplacement sur la carte.
    Remplit les champs cachés latitude / longitude, ainsi que l'adresse et le
    quartier (sélecteurs CSS) tant que l'utilisateur ne les a pas modifiés.
--}}
@props([
    'latitude' => null,
    'longitude' => null,
    'adresse' => '#adresse',
    'quartier' => '#quartier',
    'requis' => false,
    'label' => 'Emplacement sur la carte',
    'aide' => 'Cliquez sur la carte pour indiquer l’emplacement.',
])

@php
    $erreur = $errors->first('latitude') ?: $errors->first('longitude');
@endphp

<div class="cp-carte-choix mb-2 @if($erreur) is-invalid @endif"
     data-carte-choix
     data-adresse="{{ $adresse }}"
     data-quartier="{{ $quartier }}"
     @if($requis) data-requis @endif>

    <div class="d-flex flex-wrap align-items-end gap-2 mb-2">
        <div class="me-auto">
            <span class="form-label small mb-0 d-block">
                {{ $label }} @if($requis)<span class="text-danger">*</span>@endif
            </span>
        </div>
        <div class="input-group input-group-sm cp-carte-recherche">
            <input type="search" class="form-control" data-role="recherche"
                   placeholder="Rechercher un lieu, une rue…" aria-label="Rechercher un lieu">
            <button type="button" class="btn btn-outline-secondary" data-action="rechercher" title="Rechercher">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" data-action="position">
            <i class="fas fa-location-crosshairs me-1"></i>Ma position
        </button>
    </div>

    <div class="cp-carte" tabindex="0" aria-label="Carte : cliquez pour placer le repère"></div>

    <div class="d-flex justify-content-between align-items-start gap-2 mt-1 small">
        <span class="text-body-secondary" data-role="statut" aria-live="polite">{{ $erreur ?: $aide }}</span>
        <button type="button" class="btn btn-link btn-sm p-0 text-nowrap" data-action="retirer" hidden>Retirer le repère</button>
    </div>

    <input type="hidden" name="latitude" data-champ="lat" value="{{ $latitude }}">
    <input type="hidden" name="longitude" data-champ="lng" value="{{ $longitude }}">
</div>

<x-carte.scripts />
