@extends('layouts.app')

@section('title', 'Incident — ' . $incident->type_label)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $incident->type_label }} <span class="badge {{ $incident->statut === 'resolu' ? 'tone-green' : ($incident->statut === 'en_cours' ? 'tone-blue' : 'tone-amber') }} align-middle fs-6">{{ $incident->statut_label }}</span></h1>
        <p><a href="{{ route('collecteur.incidents.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Mes incidents</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Tournée</dt>
                    <dd class="col-sm-8">
                        @if($incident->itineraire)
                            <a href="{{ route('collecteur.itineraires.show', $incident->itineraire) }}">{{ $incident->itineraire->nom }}</a>
                        @else
                            —
                        @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Signalé le</dt>
                    <dd class="col-sm-8">{{ $incident->created_at->translatedFormat('l j F Y à H:i') }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Description</dt>
                    <dd class="col-sm-8">{{ $incident->description }}</dd>
                    @if($incident->admin_notes)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Réponse</dt>
                        <dd class="col-sm-8 mb-0">{{ $incident->admin_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
        @if($incident->photo)
            <div class="card mt-4">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $incident->photo) }}" alt="Photo de l’incident" class="img-fluid rounded">
                </div>
            </div>
        @endif
    </div>
    @if($incident->latitude && $incident->longitude)
        <div class="col-lg-5">
            <x-carte.apercu :latitude="$incident->latitude" :longitude="$incident->longitude" hauteur="260px" />
        </div>
    @endif
</div>
@endsection
