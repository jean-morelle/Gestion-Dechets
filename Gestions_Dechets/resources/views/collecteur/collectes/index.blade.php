@extends('layouts.app')

@section('title', 'Mes collectes')

@section('content')
<div class="page-header">
    <div>
        <h1>Mes collectes</h1>
        <p>Historique de vos passages aux points de collecte.</p>
    </div>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-sm-4 col-md-3">
        <label for="statut" class="form-label small">Résultat</label>
        <select class="form-select" id="statut" name="statut">
            <option value="">Tous</option>
            <option value="termine" @selected(request('statut') === 'termine')>Collectés</option>
            <option value="rate" @selected(request('statut') === 'rate')>Non collectés</option>
        </select>
    </div>
    <div class="col-sm-4 col-md-3">
        <label for="date_debut" class="form-label small">Du</label>
        <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
    </div>
    <div class="col-sm-4 col-md-3">
        <label for="date_fin" class="form-label small">Au</label>
        <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-outline-secondary w-100" type="submit">Filtrer</button>
    </div>
</form>

<div class="card">
    @if($collectes->isEmpty())
        <div class="empty-state">
            <i class="fas fa-dumpster" aria-hidden="true"></i>
            <p>{{ request()->hasAny(['statut', 'date_debut', 'date_fin']) ? 'Aucune collecte sur cette période.' : 'Vos passages apparaîtront ici au fil de vos tournées.' }}</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Point</th>
                        <th>Date</th>
                        <th>Résultat</th>
                        <th class="text-end">Quantité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($collectes as $collecte)
                        <tr>
                            <td class="min-w-0">
                                <a href="{{ route('collecteur.collectes.show', $collecte) }}" class="fw-medium">{{ $collecte->pointDeCollecte->nom ?? 'Point supprimé' }}</a>
                                <div class="small text-body-secondary">{{ $collecte->itineraire->nom ?? '' }}</div>
                            </td>
                            <td class="text-nowrap">
                                {{ $collecte->date_collecte?->format('d/m/Y') }}
                                <div class="small text-body-secondary">{{ $collecte->heure_fin?->format('H:i') }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $collecte->statut_tone }}">{{ $collecte->statut_label }}</span>
                                @if($collecte->statut === 'rate')<div class="small text-body-secondary">{{ $collecte->motif_echec_label }}</div>@endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if($collecte->statut === 'termine')
                                    {{ number_format((float) $collecte->quantite, 0, ',', ' ') }} kg
                                    <div class="small text-body-secondary">{{ $collecte->type_dechet_label }}</div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $collectes->links() }}</div>
@endsection
