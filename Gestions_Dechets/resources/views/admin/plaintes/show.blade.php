@extends('layouts.app')

@section('title', 'Plainte — ' . $plainte->sujet)

@php
    $statuts = \App\Http\Controllers\AdminPlainteController::STATUTS;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $plainte->sujet }}
            <span class="badge {{ ['en_attente' => 'tone-amber', 'en_cours' => 'tone-blue', 'traite' => 'tone-green'][$plainte->statut] ?? 'tone-slate' }} align-middle fs-6">{{ $statuts[$plainte->statut] ?? $plainte->statut }}</span>
        </h1>
        <p><a href="{{ route('admin.plaintes.index') }}"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Plaintes</a></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-body-secondary">Motif</dt>
                    <dd class="col-sm-8">{{ $plainte->type_plainte_label }} · priorité {{ mb_strtolower($plainte->priorite_label) }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Description</dt>
                    <dd class="col-sm-8">{{ $plainte->description }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Lieu</dt>
                    <dd class="col-sm-8">{{ $plainte->adresse }}, {{ $plainte->quartier }}</dd>
                    <dt class="col-sm-4 fw-normal text-body-secondary">Citoyen</dt>
                    <dd class="col-sm-8">
                        {{ $plainte->user->name ?? 'Compte supprimé' }}
                        @php
                            $tel = $plainte->contact_telephone ?: $plainte->user?->telephone;
                        @endphp
                        @if($tel) · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $tel) }}">{{ $tel }}</a>@endif
                    </dd>
                    @if($plainte->signalement)
                        <dt class="col-sm-4 fw-normal text-body-secondary">Signalement lié</dt>
                        <dd class="col-sm-8"><a href="{{ route('admin.signalements.show', $plainte->signalement) }}">{{ $plainte->signalement->type_dechet_label }} du {{ $plainte->signalement->created_at->format('d/m/Y') }}</a></dd>
                    @endif
                    <dt class="col-sm-4 fw-normal text-body-secondary">Reçue le</dt>
                    <dd class="col-sm-8 mb-0">{{ $plainte->created_at->translatedFormat('j F Y à H:i') }}</dd>
                </dl>
            </div>
        </div>

        @if($plainte->photo)
            <div class="card mb-4">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $plainte->photo) }}" alt="Photo jointe à la plainte" class="img-fluid rounded">
                </div>
            </div>
        @endif

        @if($plainte->latitude && $plainte->longitude)
            <x-carte.apercu :latitude="$plainte->latitude" :longitude="$plainte->longitude" :libelle="$plainte->adresse" hauteur="260px" />
        @endif
    </div>

    <div class="col-xl-5">
        <form method="POST" action="{{ route('admin.plaintes.update', $plainte) }}" class="card">
            @csrf
            @method('PUT')
            <div class="card-header"><h5>Réponse</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut">
                        @foreach($statuts as $valeur => $libelle)
                            <option value="{{ $valeur }}" @selected(old('statut', $plainte->statut) === $valeur)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <label for="reponse" class="form-label">Réponse au citoyen</label>
                <textarea class="form-control @error('reponse') is-invalid @enderror" id="reponse" name="reponse" rows="5"
                          placeholder="Expliquez ce qui a été fait ou ce qui va l’être.">{{ old('reponse', $plainte->reponse) }}</textarea>
                @error('reponse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($plainte->date_traitement)
                    <div class="form-text">Dernière mise à jour par {{ $plainte->traitePar->name ?? '—' }}, le {{ $plainte->date_traitement->translatedFormat('j F à H:i') }}.</div>
                @endif
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Enregistrer et prévenir le citoyen</button>
            </div>
        </form>
    </div>
</div>
@endsection
