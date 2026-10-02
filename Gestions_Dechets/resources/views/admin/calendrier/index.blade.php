@extends('layouts.app')

@section('title', 'Calendrier des collectes')

@php
    $types = \App\Http\Controllers\AdminCalendrierController::TYPES;
    $frequences = \App\Http\Controllers\AdminCalendrierController::FREQUENCES;
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1>Calendrier des collectes</h1>
        <p>Jours de passage par quartier, affichés aux habitants qui reçoivent un rappel la veille à 18 h.</p>
    </div>
    <a href="{{ route('admin.calendrier.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>Ajouter un passage</a>
</div>

<form method="GET" class="d-flex flex-wrap gap-2 mb-3">
    <label for="filtre-quartier" class="visually-hidden">Quartier</label>
    <select class="form-select w-auto" id="filtre-quartier" name="quartier" onchange="this.form.submit()">
        <option value="">Tous les quartiers</option>
        @foreach($quartiers as $q)
            <option value="{{ $q }}" @selected(request('quartier') === $q)>{{ $q }}</option>
        @endforeach
    </select>
    <div class="form-check align-self-center ms-2">
        <input class="form-check-input" type="checkbox" id="tous" name="statut" value="tous" @checked(request('statut') === 'tous') onchange="this.form.submit()">
        <label class="form-check-label" for="tous">Afficher aussi les passages suspendus</label>
    </div>
</form>

<div class="card">
    @if($calendriers->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-days" aria-hidden="true"></i>
            <p>Aucun passage programmé. Ajoutez les jours de collecte de chaque quartier : les habitants les verront dans leur calendrier.</p>
            <a href="{{ route('admin.calendrier.create') }}" class="btn btn-primary btn-sm">Ajouter un passage</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Quartier</th>
                        <th>Collecte</th>
                        <th>Quand</th>
                        <th>Prochain passage</th>
                        <th><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendriers as $c)
                        @php
                            $prochain = $c->getProchaineDateCollecte();
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $c->quartier }}</td>
                            <td>{{ $types[$c->type_collecte] ?? $c->type_collecte }}</td>
                            <td>
                                @if($c->frequence === 'hebdomadaire')
                                    Chaque {{ $c->jour_semaine }}
                                @elseif($c->frequence === 'ponctuelle')
                                    Le {{ $c->date_debut?->translatedFormat('j F Y') }}
                                @elseif($c->frequence === 'mensuelle')
                                    Le {{ $c->date_debut?->day ?? 1 }} de chaque mois
                                @else
                                    Tous les jours
                                @endif
                                <div class="small text-body-secondary">{{ $c->heure_debut?->format('H\hi') }} – {{ $c->heure_fin?->format('H\hi') }}</div>
                            </td>
                            <td>
                                @if($c->statut !== 'actif')
                                    <span class="badge tone-slate">Suspendu</span>
                                @elseif($prochain)
                                    {{ $prochain->isToday() ? 'Aujourd’hui' : ($prochain->isTomorrow() ? 'Demain' : ucfirst($prochain->translatedFormat('l j F'))) }}
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td class="text-end"><a href="{{ route('admin.calendrier.edit', $c) }}" class="btn btn-sm btn-outline-primary">Modifier</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $calendriers->links() }}</div>
@endsection
