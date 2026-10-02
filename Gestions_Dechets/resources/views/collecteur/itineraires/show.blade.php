@extends('layouts.app')

@section('title', $itineraire->nom)

@php
    $p = $itineraire->progression;
    $enCours = $itineraire->statut === 'en_cours';
    $etatCarte = fn ($c) => match ($c?->statut) { 'termine' => 'fait', 'rate' => 'rate', default => 'afaire' };
    // Prochaine étape à traiter : la première encore « à faire », dans l'ordre de passage
    $prochaine = $enCours
        ? $itineraire->pointsDeCollecte->first(fn ($pt) => ($collectesParPoint[$pt->id] ?? null)?->statut === 'prevue')
        : null;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $itineraire->nom }} <span class="badge {{ $itineraire->statut_tone }} align-middle fs-6">{{ $itineraire->statut_label }}</span></h1>
        <p>
            {{ $itineraire->date_debut?->translatedFormat('l j F') }}, {{ $itineraire->heure_debut?->format('H:i') }} – {{ $itineraire->heure_fin?->format('H:i') }}
            · {{ $p['total'] }} étape{{ $p['total'] > 1 ? 's' : '' }}
            @if($itineraire->distance_estimee) · {{ str_replace('.', ',', (string) (float) $itineraire->distance_estimee) }} km @endif
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($itineraire->statut === 'planifie')
            <form method="POST" action="{{ route('collecteur.itineraires.demarrer', $itineraire) }}">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fas fa-play me-1" aria-hidden="true"></i>Démarrer la tournée</button>
            </form>
        @elseif($enCours)
            <form method="POST" action="{{ route('collecteur.itineraires.terminer', $itineraire) }}"
                  @if($p['restantes']) onsubmit="return confirm('Il reste {{ $p['restantes'] }} étape(s). Elles seront marquées « non collectées ». Terminer quand même ?')" @endif>
                @csrf
                <button type="submit" class="btn {{ $p['restantes'] ? 'btn-outline-success' : 'btn-success' }}"><i class="fas fa-flag-checkered me-1" aria-hidden="true"></i>Terminer la tournée</button>
            </form>
        @endif
        @if(in_array($itineraire->statut, ['planifie', 'en_cours']))
            <a href="{{ route('collecteur.incidents.create', ['itineraire_id' => $itineraire->id]) }}" class="btn btn-outline-danger">
                <i class="fas fa-triangle-exclamation me-1" aria-hidden="true"></i>Signaler un incident
            </a>
        @endif
    </div>
</div>

@if($itineraire->description)
    <div class="alert alert-info d-flex gap-2">
        <i class="fas fa-circle-info mt-1" aria-hidden="true"></i>
        <div><strong>Consignes :</strong> {{ $itineraire->description }}</div>
    </div>
@endif

@if($itineraire->statut !== 'planifie')
    <div class="mb-3">
        <div class="d-flex justify-content-between small mb-1">
            <span>{{ $p['total'] - $p['restantes'] }}/{{ $p['total'] }} étapes faites</span>
            @if($p['kg'] > 0)<span>{{ number_format($p['kg'], 0, ',', ' ') }} kg ramassés</span>@endif
        </div>
        <div class="progress" style="height: 8px" role="progressbar" aria-label="Avancement de la tournée" aria-valuenow="{{ $p['pourcentage'] }}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar bg-success" style="width: {{ $p['pourcentage'] }}%"></div>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7 order-2 order-lg-1">
        <div class="card">
            <ol class="list-unstyled mb-0 etapes-suivi">
                @foreach($itineraire->pointsDeCollecte as $i => $point)
                    @php
                        $c = $collectesParPoint[$point->id] ?? null;
                        $estProchaine = $prochaine && $prochaine->id === $point->id;
                        $itineraireGps = 'https://www.google.com/maps/dir/?api=1&travelmode=driving&destination=' . $point->latitude . ',' . $point->longitude;
                        // Après une erreur de saisie, le formulaire concerné est rouvert
                        $ouvrirPassage = $c && old('_etape') == $c->id && old('_action') === 'passage';
                        $ouvrirEchec = $c && old('_etape') == $c->id && old('_action') === 'echec';
                    @endphp
                    <li id="etape-{{ $c?->id ?? 'p' . $point->id }}" @class(['etape-prochaine' => $estProchaine])>
                        <span class="etape-num cp-pin-{{ $etatCarte($c) }}"><span>{{ $i + 1 }}</span></span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <div class="min-w-0">
                                    <div class="fw-medium">{{ $point->nom }}</div>
                                    <div class="small text-body-secondary">{{ $point->adresse }}, {{ $point->quartier }}</div>
                                    @if($point->description)<div class="small mt-1"><i class="fas fa-circle-info text-body-secondary me-1" aria-hidden="true"></i>{{ $point->description }}</div>@endif
                                </div>
                                @if($c)<span class="badge {{ $c->statut_tone }} align-self-start">{{ $c->statut_label }}</span>@endif
                            </div>

                            @if($c?->statut === 'termine')
                                <div class="small text-body-secondary mt-1">
                                    {{ $c->heure_fin?->format('H:i') }} · {{ number_format((float) $c->quantite, 0, ',', ' ') }} kg · {{ $c->type_dechet_label }}
                                </div>
                            @elseif($c?->statut === 'rate')
                                <div class="small text-danger mt-1">{{ $c->motif_echec_label }}</div>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a href="{{ $itineraireGps }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-diamond-turn-right me-1" aria-hidden="true"></i>Y aller
                                </a>
                                @if($enCours && $c?->statut === 'prevue')
                                    <button class="btn btn-sm btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#passage-{{ $c->id }}" aria-expanded="{{ $ouvrirPassage ? 'true' : 'false' }}" aria-controls="passage-{{ $c->id }}">
                                        <i class="fas fa-camera me-1" aria-hidden="true"></i>Valider le passage
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#echec-{{ $c->id }}" aria-expanded="{{ $ouvrirEchec ? 'true' : 'false' }}" aria-controls="echec-{{ $c->id }}">
                                        Non collecté
                                    </button>
                                @endif
                            </div>

                            @if($enCours && $c?->statut === 'prevue')
                                {{-- Valider le passage --}}
                                <form method="POST" action="{{ route('collecteur.collectes.passage', $c) }}" enctype="multipart/form-data"
                                      class="collapse passage-form border rounded p-3 mt-3 @if($ouvrirPassage) show @endif" id="passage-{{ $c->id }}" data-gps>
                                    @csrf
                                    <input type="hidden" name="_etape" value="{{ $c->id }}">
                                    <input type="hidden" name="_action" value="passage">
                                    <input type="hidden" name="latitude" data-gps-lat>
                                    <input type="hidden" name="longitude" data-gps-lng>
                                    <input type="hidden" name="precision" data-gps-precision>

                                    <div class="mb-3">
                                        <label for="photo-{{ $c->id }}" class="form-label">Photo du point après ramassage</label>
                                        <input type="file" class="form-control @if($ouvrirPassage) @error('photo') is-invalid @enderror @endif" id="photo-{{ $c->id }}" name="photo" accept="image/*" capture="environment" required>
                                        @if($ouvrirPassage) @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-7">
                                            <label for="type-{{ $c->id }}" class="form-label">Type de déchet</label>
                                            <select class="form-select" id="type-{{ $c->id }}" name="type_dechet" required>
                                                @foreach(\App\Models\Collecte::TYPES_DECHET as $valeur => $libelle)
                                                    <option value="{{ $valeur }}" @selected(($ouvrirPassage ? old('type_dechet') : null) === $valeur)>{{ $libelle }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="quantite-{{ $c->id }}" class="form-label">Quantité (kg)</label>
                                            <input type="number" inputmode="decimal" min="0" step="1" class="form-control @if($ouvrirPassage) @error('quantite') is-invalid @enderror @endif" id="quantite-{{ $c->id }}" name="quantite" value="{{ $ouvrirPassage ? old('quantite') : '' }}" placeholder="Estimation" required>
                                            @if($ouvrirPassage) @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes-{{ $c->id }}" class="form-label">Remarque <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                                        <input type="text" class="form-control" id="notes-{{ $c->id }}" name="notes" value="{{ $ouvrirPassage ? old('notes') : '' }}" placeholder="Ex. : bac abîmé, débordement">
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <span class="small text-body-secondary" data-gps-statut aria-live="polite"><i class="fas fa-location-crosshairs me-1" aria-hidden="true"></i>Localisation…</span>
                                        <button type="submit" class="btn btn-success">Enregistrer le passage</button>
                                    </div>
                                </form>

                                {{-- Point non collecté --}}
                                <form method="POST" action="{{ route('collecteur.collectes.echec', $c) }}"
                                      class="collapse border rounded p-3 mt-3 @if($ouvrirEchec) show @endif" id="echec-{{ $c->id }}">
                                    @csrf
                                    <input type="hidden" name="_etape" value="{{ $c->id }}">
                                    <input type="hidden" name="_action" value="echec">
                                    <fieldset class="mb-3">
                                        <legend class="form-label fs-6">Pourquoi ce point n’a-t-il pas été collecté ?</legend>
                                        @foreach(\App\Models\Collecte::MOTIFS_ECHEC as $valeur => $libelle)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="motif_echec" id="motif-{{ $c->id }}-{{ $valeur }}" value="{{ $valeur }}" @checked($ouvrirEchec && old('motif_echec') === $valeur) required>
                                                <label class="form-check-label" for="motif-{{ $c->id }}-{{ $valeur }}">{{ $libelle }}</label>
                                            </div>
                                        @endforeach
                                        @if($ouvrirEchec) @error('motif_echec')<div class="text-danger small">{{ $message }}</div>@enderror @endif
                                    </fieldset>
                                    <div class="mb-3">
                                        <label for="precision-{{ $c->id }}" class="form-label">Précision <span class="text-body-secondary fw-normal">(obligatoire pour « Autre raison »)</span></label>
                                        <input type="text" class="form-control @if($ouvrirEchec) @error('notes') is-invalid @enderror @endif" id="precision-{{ $c->id }}" name="notes" value="{{ $ouvrirEchec ? old('notes') : '' }}">
                                        @if($ouvrirEchec) @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-danger">Confirmer</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="col-lg-5 order-1 order-lg-2">
        <x-carte.points relier numeroter hauteur="320px"
            :points="$itineraire->pointsDeCollecte->map(fn ($pt) => [
                'lat' => $pt->latitude, 'lng' => $pt->longitude, 'nom' => $pt->nom, 'detail' => $pt->adresse,
                'etat' => $etatCarte($collectesParPoint[$pt->id] ?? null),
            ])" />
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Position GPS relevée à l'ouverture du formulaire de passage (preuve de présence)
    document.querySelectorAll('form[data-gps]').forEach(function (form) {
        var statut = form.querySelector('[data-gps-statut]');
        var releve = false;

        function localiser() {
            if (releve) return;
            releve = true;
            if (!navigator.geolocation) {
                statut.textContent = 'GPS indisponible : le passage sera enregistré sans position.';
                return;
            }
            navigator.geolocation.getCurrentPosition(function (pos) {
                form.querySelector('[data-gps-lat]').value = pos.coords.latitude;
                form.querySelector('[data-gps-lng]').value = pos.coords.longitude;
                form.querySelector('[data-gps-precision]').value = Math.round(pos.coords.accuracy);
                statut.innerHTML = '<i class="fas fa-location-crosshairs me-1 text-success" aria-hidden="true"></i>Position relevée (± ' + Math.round(pos.coords.accuracy) + ' m)';
            }, function () {
                releve = false;
                statut.textContent = 'Position non disponible : le passage sera enregistré sans GPS.';
            }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 });
        }

        form.addEventListener('show.bs.collapse', localiser);
        if (form.classList.contains('show')) localiser();
    });

    // Ouvrir directement l'étape concernée après un enregistrement
    if (location.hash) {
        var cible = document.querySelector(location.hash);
        if (cible) cible.scrollIntoView({ block: 'center' });
    }
</script>
@endpush
