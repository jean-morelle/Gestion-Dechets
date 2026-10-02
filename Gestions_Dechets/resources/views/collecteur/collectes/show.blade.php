@extends('layouts.app')

@section('title', 'Collecte — ' . ($collecte->pointDeCollecte->nom ?? 'détail'))

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $collecte->pointDeCollecte->nom ?? 'Point supprimé' }} <span class="badge {{ $collecte->statut_tone }} align-middle fs-6">{{ $collecte->statut_label }}</span></h1>
        <p>
            @if($collecte->itineraire)
                <a href="{{ route('collecteur.itineraires.show', $collecte->itineraire) }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>{{ $collecte->itineraire->nom }}</a>
            @else
                <a href="{{ route('collecteur.collectes.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Mes collectes</a>
            @endif
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Adresse</dt>
                    <dd class="col-sm-8">{{ $collecte->pointDeCollecte->adresse ?? '—' }}, {{ $collecte->pointDeCollecte->quartier ?? '' }}</dd>

                    <dt class="col-sm-4 fw-normal text-body-secondary">Date</dt>
                    <dd class="col-sm-8">{{ $collecte->date_collecte?->translatedFormat('l j F Y') }}@if($collecte->heure_fin), {{ $collecte->heure_fin->format('H:i') }}@endif</dd>

                    @if($collecte->statut === 'termine')
                        <dt class="col-sm-4 fw-normal text-body-secondary">Ramassé</dt>
                        <dd class="col-sm-8">{{ number_format((float) $collecte->quantite, 0, ',', ' ') }} kg · {{ $collecte->type_dechet_label }}</dd>

                        <dt class="col-sm-4 fw-normal text-body-secondary">Position</dt>
                        <dd class="col-sm-8">
                            @if($collecte->distance_point === null)
                                Non relevée
                            @elseif($collecte->ecart_gps_suspect)
                                <span class="text-danger">À {{ $collecte->distance_point }} m du point prévu</span>
                            @else
                                <span class="text-success">Sur place</span> (à {{ $collecte->distance_point }} m)
                            @endif
                        </dd>
                    @elseif($collecte->statut === 'rate')
                        <dt class="col-sm-4 fw-normal text-body-secondary">Raison</dt>
                        <dd class="col-sm-8">{{ $collecte->motif_echec_label }}</dd>
                    @endif

                    @if($collecte->notes)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Remarque</dt>
                        <dd class="col-sm-8 mb-0">{{ $collecte->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($collecte->photo_validation)
            <div class="card mt-4">
                <div class="card-header"><h5>Photo du passage</h5></div>
                <div class="card-body">
                    <img src="{{ asset('storage/' . $collecte->photo_validation) }}" alt="Photo du point après le passage" class="img-fluid rounded">
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-5">
        @if($collecte->pointDeCollecte)
            <x-carte.apercu :latitude="$collecte->pointDeCollecte->latitude" :longitude="$collecte->pointDeCollecte->longitude" :libelle="$collecte->pointDeCollecte->nom" hauteur="280px" />
        @endif
    </div>
</div>
@endsection
