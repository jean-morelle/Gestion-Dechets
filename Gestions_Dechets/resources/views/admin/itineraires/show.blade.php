@extends('layouts.app')

@section('title', $itineraire->nom)

@php
    $p = $itineraire->progression;
    $etatCarte = fn ($collecte) => match ($collecte?->statut) {
        'termine' => 'fait',
        'rate' => 'rate',
        default => 'afaire',
    };
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $itineraire->nom }} <span class="badge {{ $itineraire->statut_tone }} align-middle fs-6">{{ $itineraire->statut_label }}</span></h1>
        <p>
            {{ $itineraire->collecteur->name ?? 'Collecteur supprimé' }} ·
            {{ $itineraire->date_debut?->translatedFormat('l j F Y') }},
            {{ $itineraire->heure_debut?->format('H:i') }} – {{ $itineraire->heure_fin?->format('H:i') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($itineraire->peutEtreModifie())
            <a href="{{ route('admin.itineraires.edit', $itineraire) }}" class="btn btn-outline-primary">Modifier</a>
            <form method="POST" action="{{ route('admin.itineraires.destroy', $itineraire) }}" onsubmit="return confirm('Supprimer cette tournée ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
            </form>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <span class="stat-icon tone-slate"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
            <div><div class="stat-value">{{ $p['total'] }}</div><div class="stat-label">Étapes</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <span class="stat-icon tone-green"><i class="fas fa-check" aria-hidden="true"></i></span>
            <div><div class="stat-value">{{ $p['collectees'] }}</div><div class="stat-label">Collectées</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <span class="stat-icon tone-red"><i class="fas fa-xmark" aria-hidden="true"></i></span>
            <div><div class="stat-value">{{ $p['ratees'] }}</div><div class="stat-label">Non collectées</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <span class="stat-icon tone-blue"><i class="fas fa-weight-hanging" aria-hidden="true"></i></span>
            <div><div class="stat-value">{{ number_format($p['kg'], 0, ',', ' ') }} kg</div><div class="stat-label">Ramassés</div></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header"><h5>Déroulement</h5></div>
            <ol class="list-unstyled mb-0 etapes-suivi">
                @foreach($itineraire->pointsDeCollecte as $i => $point)
                    @php
                        $c = $collectesParPoint[$point->id] ?? null;
                    @endphp
                    <li>
                        <span class="etape-num cp-pin-{{ $etatCarte($c) }}"><span>{{ $i + 1 }}</span></span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <div>
                                    <div class="fw-medium">{{ $point->nom }}</div>
                                    <div class="small text-body-secondary">{{ $point->quartier }} · {{ $point->adresse }}</div>
                                </div>
                                @if($c)
                                    <span class="badge {{ $c->statut_tone }} align-self-start">{{ $c->statut_label }}</span>
                                @endif
                            </div>

                            @if($c && $c->statut === 'termine')
                                <div class="small mt-2">
                                    {{ $c->heure_fin?->format('H:i') }} ·
                                    {{ $c->quantite !== null ? number_format((float) $c->quantite, 0, ',', ' ') . ' kg' : 'quantité non saisie' }}
                                    @if($c->type_dechet_label) · {{ $c->type_dechet_label }} @endif
                                    @if($c->ecart_gps_suspect)
                                        · <span class="text-danger" title="Position enregistrée loin du point prévu"><i class="fas fa-location-crosshairs" aria-hidden="true"></i> validé à {{ $c->distance_point }} m du point</span>
                                    @elseif($c->distance_point !== null)
                                        · <span class="text-success"><i class="fas fa-location-crosshairs" aria-hidden="true"></i> sur place</span>
                                    @else
                                        · <span class="text-body-secondary">sans position GPS</span>
                                    @endif
                                </div>
                                @if($c->notes)<div class="small text-body-secondary mt-1">« {{ $c->notes }} »</div>@endif
                                @if($c->photo_validation)
                                    <a href="{{ asset('storage/' . $c->photo_validation) }}" target="_blank" rel="noopener" class="d-inline-block mt-2">
                                        <img src="{{ asset('storage/' . $c->photo_validation) }}" alt="Photo du passage à {{ $point->nom }}" class="rounded border" style="height: 72px; width: 96px; object-fit: cover">
                                    </a>
                                @endif
                            @elseif($c && $c->statut === 'rate')
                                <div class="small mt-2 text-danger">{{ $c->motif_echec_label }}</div>
                                @if($c->notes)<div class="small text-body-secondary mt-1">« {{ $c->notes }} »</div>@endif
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        @if($itineraire->description)
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Consignes</h2>
                    <p class="mb-0">{{ $itineraire->description }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-5">
        <x-carte.points relier numeroter hauteur="360px" class="mb-4"
            :points="$itineraire->pointsDeCollecte->map(fn ($pt) => [
                'lat' => $pt->latitude, 'lng' => $pt->longitude, 'nom' => $pt->nom, 'detail' => $pt->quartier,
                'etat' => $etatCarte($collectesParPoint[$pt->id] ?? null),
            ])" />

        <div class="card">
            <div class="card-header"><h5>Incidents signalés</h5></div>
            @forelse($itineraire->incidents as $incident)
                <div class="card-body border-bottom">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="fw-medium">{{ $incident->type_label }}</span>
                        <span class="small text-body-secondary text-nowrap">{{ $incident->created_at->format('H:i') }}</span>
                    </div>
                    <p class="small mb-1">{{ $incident->description }}</p>
                    @if($incident->photo)
                        <a href="{{ asset('storage/' . $incident->photo) }}" target="_blank" rel="noopener" class="small">Voir la photo</a>
                    @endif
                </div>
            @empty
                <div class="card-body small text-body-secondary">Aucun incident sur cette tournée.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
