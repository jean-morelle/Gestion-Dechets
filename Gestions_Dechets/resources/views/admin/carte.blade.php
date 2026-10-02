@extends('layouts.app')

@section('title', 'Carte de la commune')

@php
    $calques = [
        'signalements' => ['Signalements à traiter', 'rate'],
        'demandes' => ['Demandes de collecte', 'bleu'],
        'incidents' => ['Incidents en cours', 'afaire'],
        'points' => ['Points de collecte', 'fait'],
    ];
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Carte de la commune</h1>
        <p>Tout ce qui attend une intervention, au même endroit. Cliquez sur un repère pour ouvrir la fiche.</p>
    </div>
</div>

<div class="d-flex flex-wrap gap-3 mb-3" id="calques" role="group" aria-label="Éléments affichés">
    @foreach($calques as $cle => [$libelle, $etat])
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="calque-{{ $cle }}" value="{{ $cle }}" @checked($cle !== 'points')>
            <label class="form-check-label d-flex align-items-center gap-2" for="calque-{{ $cle }}">
                <span class="legende-pastille cp-pin-{{ $etat }}" aria-hidden="true"></span>
                {{ $libelle }} <span class="text-body-secondary">({{ $compteurs[$cle] }})</span>
            </label>
        </div>
    @endforeach
</div>

<x-carte.points id="carte-commune" hauteur="calc(100vh - 260px)" :points="[]" />

@if($elements->isEmpty())
    <p class="small text-body-secondary mt-2">Rien à afficher pour l’instant : les éléments apparaissent dès qu’ils ont une position sur la carte.</p>
@endif
@endsection

@push('scripts')
<script>
(function () {
    var elements = @json($elements);
    var carte = document.getElementById('carte-commune');

    function afficher() {
        var actifs = Array.prototype.map.call(document.querySelectorAll('#calques input:checked'), function (c) { return c.value; });
        carte.dispatchEvent(new CustomEvent('cp:points', {
            detail: elements.filter(function (e) { return actifs.indexOf(e.calque) !== -1; })
        }));
    }

    document.querySelectorAll('#calques input').forEach(function (c) { c.addEventListener('change', afficher); });
    if (document.readyState === 'complete') afficher(); else window.addEventListener('load', afficher);
})();
</script>
@endpush
