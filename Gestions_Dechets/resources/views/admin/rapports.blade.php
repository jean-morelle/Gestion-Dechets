@extends('layouts.app')

@section('title', 'Rapport d’activité')

@php
    $filtres = ['du' => $du->toDateString(), 'au' => $au->toDateString(), 'quartier' => $quartier];
    $kg = fn ($v) => $v >= 1000 ? number_format($v / 1000, 1, ',', ' ') . ' t' : number_format($v, 0, ',', ' ') . ' kg';
    $pct = fn ($a, $b) => $b ? round($a / $b * 100) . ' %' : '—';
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Rapport d’activité</h1>
        <p>
            Du {{ $du->translatedFormat('j F Y') }} au {{ $au->translatedFormat('j F Y') }}
            @if($quartier) · {{ $quartier }} @else · toute la commune @endif
        </p>
    </div>
    <button type="button" class="btn btn-outline-primary d-print-none" onclick="window.print()">
        <i class="fas fa-print me-1" aria-hidden="true"></i>Imprimer / PDF
    </button>
</div>

<form method="GET" class="row g-2 align-items-end mb-4 d-print-none">
    <div class="col-sm-4 col-md-3">
        <label for="du" class="form-label small">Du</label>
        <input type="date" class="form-control" id="du" name="du" value="{{ $du->toDateString() }}">
    </div>
    <div class="col-sm-4 col-md-3">
        <label for="au" class="form-label small">Au</label>
        <input type="date" class="form-control" id="au" name="au" value="{{ $au->toDateString() }}">
    </div>
    <div class="col-sm-4 col-md-3">
        <label for="quartier" class="form-label small">Quartier</label>
        <select class="form-select" id="quartier" name="quartier">
            <option value="">Toute la commune</option>
            @foreach($quartiers as $q)
                <option value="{{ $q }}" @selected($quartier === $q)>{{ $q }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-grid">
        <button class="btn btn-primary" type="submit">Afficher</button>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <span class="stat-icon tone-blue"><i class="fas fa-weight-hanging" aria-hidden="true"></i></span>
            <span><span class="stat-value d-block">{{ $kg($chiffres['kg']) }}</span><span class="stat-label">collectés en {{ $chiffres['passages'] }} passage{{ $chiffres['passages'] > 1 ? 's' : '' }}</span></span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <span class="stat-icon tone-green"><i class="fas fa-check" aria-hidden="true"></i></span>
            <span><span class="stat-value d-block">{{ $pct($chiffres['signalements_traites'], $chiffres['signalements']) }}</span><span class="stat-label">des {{ $chiffres['signalements'] }} signalements traités</span></span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <span class="stat-icon tone-amber"><i class="fas fa-stopwatch" aria-hidden="true"></i></span>
            <span>
                <span class="stat-value d-block">
                    @if($chiffres['delai_moyen_heures'] === null) —
                    @elseif($chiffres['delai_moyen_heures'] < 48) {{ $chiffres['delai_moyen_heures'] }} h
                    @else {{ round($chiffres['delai_moyen_heures'] / 24, 1) }} j
                    @endif
                </span>
                <span class="stat-label">délai moyen de traitement</span>
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <span class="stat-icon tone-red"><i class="fas fa-xmark" aria-hidden="true"></i></span>
            <span><span class="stat-value d-block">{{ $chiffres['passages_rates'] }}</span><span class="stat-label">points non collectés</span></span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h5>Demandes des habitants</h5></div>
            <table class="table mb-0">
                <tbody>
                    <tr><td>Signalements reçus</td><td class="text-end fw-medium">{{ $chiffres['signalements'] }}</td></tr>
                    <tr><td>Demandes de collecte reçues</td><td class="text-end fw-medium">{{ $chiffres['demandes'] }}</td></tr>
                    <tr><td class="ps-4 text-body-secondary">dont effectuées</td><td class="text-end">{{ $chiffres['demandes_effectuees'] }}</td></tr>
                    <tr><td class="ps-4 text-body-secondary">dont refusées</td><td class="text-end">{{ $chiffres['demandes_refusees'] }}</td></tr>
                    <tr><td>Plaintes reçues</td><td class="text-end fw-medium">{{ $chiffres['plaintes'] }}</td></tr>
                    <tr><td class="ps-4 text-body-secondary">dont répondues</td><td class="text-end">{{ $chiffres['plaintes_repondues'] }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h5>Tournées</h5></div>
            <table class="table mb-0">
                <tbody>
                    <tr><td>Points collectés</td><td class="text-end fw-medium">{{ $chiffres['passages'] }}</td></tr>
                    <tr><td>Points non collectés</td><td class="text-end fw-medium">{{ $chiffres['passages_rates'] }}</td></tr>
                    <tr><td>Taux de réalisation</td><td class="text-end fw-medium">{{ $pct($chiffres['passages'], $chiffres['passages'] + $chiffres['passages_rates']) }}</td></tr>
                    <tr><td>Passages confirmés sur place (GPS)</td><td class="text-end fw-medium">{{ $pct($chiffres['gps_valides'], $chiffres['passages']) }}</td></tr>
                    <tr><td>Quantité collectée</td><td class="text-end fw-medium">{{ $kg($chiffres['kg']) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><h5>Par quartier</h5></div>
    @if($parQuartier->isEmpty())
        <div class="card-body text-body-secondary small">Aucune activité sur cette période.</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Quartier</th>
                        <th class="text-end">Signalements</th>
                        <th class="text-end">dont traités</th>
                        <th class="text-end">Demandes</th>
                        <th class="text-end">Collecté</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parQuartier as $ligne)
                        <tr>
                            <td>{{ $ligne['quartier'] }}</td>
                            <td class="text-end">{{ $ligne['signalements'] }}</td>
                            <td class="text-end">{{ $ligne['signalements_traites'] }}</td>
                            <td class="text-end">{{ $ligne['demandes'] }}</td>
                            <td class="text-end">{{ $kg($ligne['kg']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card d-print-none">
    <div class="card-body">
        <h2 class="fs-6 fw-semibold">Exporter les données de la période (Excel)</h2>
        <div class="d-flex flex-wrap gap-2">
            @foreach(['signalements' => 'Signalements', 'demandes' => 'Demandes de collecte', 'plaintes' => 'Plaintes', 'passages' => 'Passages des tournées'] as $jeu => $libelle)
                <a href="{{ route('admin.rapports.export', ['jeu' => $jeu] + array_filter($filtres)) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-csv me-1" aria-hidden="true"></i>{{ $libelle }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<p class="d-none d-print-block small text-body-secondary mt-4">
    Rapport généré le {{ now()->translatedFormat('j F Y à H:i') }} par {{ auth()->user()->name }} — {{ config('app.name') }}
</p>
@endsection
