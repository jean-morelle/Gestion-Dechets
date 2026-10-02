@extends('layouts.app')

@php
    $edition = $itineraire->exists;
    $pointsJs = $points->map(fn ($p) => [
        'id' => $p->id, 'nom' => $p->nom, 'quartier' => $p->quartier, 'adresse' => $p->adresse,
        'lat' => $p->latitude, 'lng' => $p->longitude,
    ])->values();
@endphp

@section('title', $edition ? 'Modifier la tournée' : 'Nouvelle tournée')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $edition ? $itineraire->nom : 'Nouvelle tournée' }}</h1>
        <p><a href="{{ $edition ? route('admin.itineraires.show', $itineraire) : route('admin.itineraires.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>{{ $edition ? 'Retour à la tournée' : 'Tournées' }}</a></p>
    </div>
</div>

@if($collecteurs->isEmpty())
    <div class="alert alert-warning">
        Aucun collecteur actif : créez d’abord un compte collecteur pour pouvoir lui confier une tournée.
    </div>
@endif

<form method="POST" action="{{ $edition ? route('admin.itineraires.update', $itineraire) : route('admin.itineraires.store') }}" id="form-tournee">
    @csrf
    @if($edition) @method('PUT') @endif

    <div class="card mb-4">
        <div class="card-header"><h5>Informations</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom de la tournée</label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $itineraire->nom) }}" placeholder="Ex. : Bè – marchés du matin" required>
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="collecteur_id" class="form-label">Collecteur</label>
                    <select class="form-select @error('collecteur_id') is-invalid @enderror" id="collecteur_id" name="collecteur_id" required>
                        <option value="">Choisir…</option>
                        @foreach($collecteurs as $c)
                            <option value="{{ $c->id }}" @selected((int) old('collecteur_id', $itineraire->collecteur_id) === $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('collecteur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-6 col-lg-3">
                    <label for="type" class="form-label">Fréquence</label>
                    <select class="form-select" id="type" name="type">
                        @foreach(\App\Models\Itineraire::TYPES as $valeur => $libelle)
                            <option value="{{ $valeur }}" @selected(old('type', $itineraire->type) === $valeur)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="date_debut" class="form-label">Date</label>
                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $itineraire->date_debut?->format('Y-m-d')) }}" required>
                    @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="heure_debut" class="form-label">Départ</label>
                    <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $itineraire->heure_debut?->format('H:i')) }}" required>
                    @error('heure_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="heure_fin" class="form-label">Fin prévue</label>
                    <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" value="{{ old('heure_fin', $itineraire->heure_fin?->format('H:i')) }}" required>
                    @error('heure_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3" id="bloc-date-fin">
                    <label for="date_fin" class="form-label">Jusqu’au <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $itineraire->date_fin?->format('Y-m-d')) }}">
                    @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Consignes pour le collecteur <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Ex. : commencer par le marché avant l’ouverture des étals">{{ old('description', $itineraire->description) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Étapes de la tournée</h5>
            <span class="small text-body-secondary" id="resume-etapes" aria-live="polite"></span>
        </div>
        <div class="card-body">
            @error('points')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror

            @if($points->isEmpty())
                <div class="empty-state py-3">
                    <i class="fas fa-map-location-dot" aria-hidden="true"></i>
                    <p>Aucun point de collecte actif. Ajoutez d’abord les points par lesquels passera la tournée.</p>
                    <a href="{{ route('admin.points.create') }}" class="btn btn-primary btn-sm">Ajouter un point de collecte</a>
                </div>
            @else
                <div class="row g-4">
                    <div class="col-lg-5">
                        <label for="recherche-point" class="form-label">Points disponibles</label>
                        <input type="search" class="form-control mb-2" id="recherche-point" placeholder="Filtrer par nom ou quartier…" autocomplete="off">
                        <ul class="list-group etapes-dispo" id="points-dispo"></ul>
                    </div>
                    <div class="col-lg-7">
                        <span class="form-label d-block">Ordre de passage</span>
                        <ol class="list-group list-group-numbered mb-3" id="etapes">
                            {{-- Rendu initial (sans JavaScript, la sélection actuelle est conservée) --}}
                            @foreach($selection as $id)
                                <li class="list-group-item"><input type="hidden" name="points[]" value="{{ $id }}">{{ $points->firstWhere('id', $id)?->nom }}</li>
                            @endforeach
                        </ol>
                        <x-carte.points id="carte-etapes" relier numeroter hauteur="300px" :points="[]" />
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ $edition ? route('admin.itineraires.show', $itineraire) : route('admin.itineraires.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">{{ $edition ? 'Enregistrer' : 'Planifier la tournée' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var points = @json($pointsJs);
    var selection = @json($selection);
    var parId = {};
    points.forEach(function (p) { parId[p.id] = p; });
    selection = selection.filter(function (id) { return parId[id]; });

    var listeDispo = document.getElementById('points-dispo');
    var listeEtapes = document.getElementById('etapes');
    var recherche = document.getElementById('recherche-point');
    var resume = document.getElementById('resume-etapes');
    var carte = document.getElementById('carte-etapes');
    if (!listeDispo) return;

    function distanceKm(a, b) {
        var R = 6371, rad = Math.PI / 180;
        var dLat = (b.lat - a.lat) * rad, dLng = (b.lng - a.lng) * rad;
        var x = Math.sin(dLat / 2) ** 2 + Math.cos(a.lat * rad) * Math.cos(b.lat * rad) * Math.sin(dLng / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
    }

    function bouton(icone, libelle, action) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'btn btn-sm btn-link text-body-secondary px-1';
        b.setAttribute('aria-label', libelle);
        b.title = libelle;
        b.innerHTML = '<i class="fas ' + icone + '" aria-hidden="true"></i>';
        b.addEventListener('click', action);
        return b;
    }

    function rendre() {
        // Étapes choisies, dans l'ordre
        listeEtapes.replaceChildren();
        selection.forEach(function (id, i) {
            var p = parId[id];
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex align-items-center gap-2';
            li.innerHTML = '<input type="hidden" name="points[]" value="' + id + '">'
                + '<div class="flex-grow-1 min-w-0"><div class="fw-medium text-truncate"></div><div class="small text-body-secondary text-truncate"></div></div>';
            li.querySelector('.fw-medium').textContent = p.nom;
            li.querySelector('.small').textContent = p.quartier + ' · ' + p.adresse;
            var actions = document.createElement('div');
            actions.className = 'text-nowrap';
            if (i > 0) actions.appendChild(bouton('fa-arrow-up', 'Monter', function () { deplacer(i, -1); }));
            if (i < selection.length - 1) actions.appendChild(bouton('fa-arrow-down', 'Descendre', function () { deplacer(i, 1); }));
            actions.appendChild(bouton('fa-xmark', 'Retirer', function () { selection.splice(i, 1); rendre(); }));
            li.appendChild(actions);
            listeEtapes.appendChild(li);
        });
        if (!selection.length) {
            var vide = document.createElement('li');
            vide.className = 'list-group-item text-body-secondary small';
            vide.textContent = 'Ajoutez les points dans l’ordre où le collecteur doit passer.';
            listeEtapes.appendChild(vide);
        }

        // Points encore disponibles, filtrés
        var filtre = recherche.value.trim().toLowerCase();
        listeDispo.replaceChildren();
        points.filter(function (p) {
            return selection.indexOf(p.id) === -1
                && (!filtre || (p.nom + ' ' + p.quartier + ' ' + p.adresse).toLowerCase().indexOf(filtre) !== -1);
        }).forEach(function (p) {
            var li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action d-flex align-items-center gap-2';
            li.innerHTML = '<div class="flex-grow-1 min-w-0"><div class="fw-medium text-truncate"></div><div class="small text-body-secondary text-truncate"></div></div>';
            li.querySelector('.fw-medium').textContent = p.nom;
            li.querySelector('.small').textContent = p.quartier;
            var ajouter = bouton('fa-plus', 'Ajouter à la tournée', function () { selection.push(p.id); rendre(); });
            ajouter.className = 'btn btn-sm btn-outline-primary';
            li.appendChild(ajouter);
            listeDispo.appendChild(li);
        });

        // Résumé et carte
        var km = 0;
        for (var i = 1; i < selection.length; i++) km += distanceKm(parId[selection[i - 1]], parId[selection[i]]);
        resume.textContent = selection.length
            ? selection.length + ' étape' + (selection.length > 1 ? 's' : '') + ' · ' + km.toFixed(1).replace('.', ',') + ' km à vol d’oiseau'
            : '';
        if (carte) {
            carte.dispatchEvent(new CustomEvent('cp:points', {
                detail: selection.map(function (id) { var p = parId[id]; return { lat: p.lat, lng: p.lng, nom: p.nom, detail: p.quartier }; })
            }));
        }
    }

    function deplacer(i, sens) {
        var j = i + sens;
        var tmp = selection[i]; selection[i] = selection[j]; selection[j] = tmp;
        rendre();
    }

    recherche.addEventListener('input', rendre);
    recherche.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });

    // La carte s'initialise au chargement de la page : on attend qu'elle soit prête
    if (document.readyState === 'complete') rendre();
    else window.addEventListener('load', rendre);
})();
</script>
@endpush
