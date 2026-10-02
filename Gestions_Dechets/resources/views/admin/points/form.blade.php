@extends('layouts.app')

@php
    $edition = $point->exists;
@endphp

@section('title', $edition ? 'Modifier un point' : 'Nouveau point de collecte')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $edition ? $point->nom : 'Nouveau point de collecte' }}</h1>
        <p><a href="{{ route('admin.points.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Points de collecte</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <form method="POST" action="{{ $edition ? route('admin.points.update', $point) : route('admin.points.store') }}" class="card">
            @csrf
            @if($edition) @method('PUT') @endif
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="nom" class="form-label">Nom du point</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $point->nom) }}" placeholder="Ex. : Bac du marché de Bè" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            @foreach(\App\Models\PointDeCollecte::TYPES as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('type', $point->type) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <x-carte.choix :latitude="old('latitude', $point->latitude)" :longitude="old('longitude', $point->longitude)" requis
                                       aide="Cliquez sur l’emplacement exact du bac ou du dépôt. L’adresse et le quartier se remplissent tout seuls." />
                    </div>

                    <div class="col-md-8">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse', $point->adresse) }}" required>
                        @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="quartier" class="form-label">Quartier</label>
                        <input type="text" class="form-control @error('quartier') is-invalid @enderror" id="quartier" name="quartier" value="{{ old('quartier', $point->quartier) }}" list="liste-quartiers" autocomplete="off" required>
                        <datalist id="liste-quartiers">
                            @foreach(config('quartiers') as $q)<option value="{{ $q }}">@endforeach
                        </datalist>
                        @error('quartier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            @foreach(\App\Models\PointDeCollecte::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('statut', $point->statut) === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Seuls les points actifs peuvent être ajoutés à une tournée.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="capacite" class="form-label">Capacité <span class="text-body-secondary fw-normal">(litres, facultatif)</span></label>
                        <input type="number" min="0" class="form-control @error('capacite') is-invalid @enderror" id="capacite" name="capacite" value="{{ old('capacite', $point->capacite) }}" placeholder="Ex. : 660">
                        @error('capacite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4"></div>

                    <div class="col-md-6">
                        <label for="contact_responsable" class="form-label">Responsable sur place <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="text" class="form-control" id="contact_responsable" name="contact_responsable" value="{{ old('contact_responsable', $point->contact_responsable) }}" placeholder="Ex. : chef de marché">
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $point->telephone) }}" placeholder="+228 90 12 34 56">
                        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Indications pour le collecteur <span class="text-body-secondary fw-normal">(facultatif)</span></label>
                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Ex. : bac derrière la station, accès par la rue pavée">{{ old('description', $point->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('admin.points.index') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">{{ $edition ? 'Enregistrer' : 'Ajouter le point' }}</button>
            </div>
        </form>
    </div>

    @if($edition)
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-6 fw-semibold">Supprimer ce point</h2>
                    <p class="small text-body-secondary">S’il figure déjà dans une tournée, il sera seulement désactivé pour conserver l’historique des collectes.</p>
                    <form method="POST" action="{{ route('admin.points.destroy', $point) }}" onsubmit="return confirm('Supprimer ce point de collecte ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
